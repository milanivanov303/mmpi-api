<?php

namespace Modules\ProjectEvents\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\ProjectEvents\Exports\Sheets\EventsPerMonthSheet;

class ProjectEventsExport implements WithMultipleSheets, Responsable
{
    use Exportable;

    /**
     * filter for excel export file
     * @var array
     */
    private $filter;

    /**
     * name of the exported file
     * @var string
     */
    private $fileName;

    /**
     * Export event constructor
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->filter = $request->all();
        $this->fileName = $this->constructFileName($this->filter);
    }

    /**
     * @param array $filter
     * @return string
     */
    public function constructFileName(Array $filter): string
    {
        $startDate = str_replace($filter['start_date'], '.', '-');
        $endDate = str_replace($filter['end_date'], '.', '-');
        return "project_events_{$startDate}-{$endDate}.xlsx";
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        //set startDate and endDate from dd.MM.yyyy format to object
        $startDate = explode('.', $this->filter['start_date']);
        $startDate = [
            "day" => (int)$startDate[0],
            "month" => (int)$startDate[1],
            "year" => (int)$startDate[2],
        ];
        $endDate = explode('.', $this->filter['end_date']);
        $endDate = [
            "day" => (int)$endDate[0],
            "month" => (int)$endDate[1],
            "year" => (int)$endDate[2],
        ];
        $currentYear = $startDate['year'];

        //if the whole period is in the same month and year make only one sheet
        if ($startDate['month'] === $endDate['month'] && $startDate['year'] === $endDate['year']) {
            $sheets[] = new EventsPerMonthSheet(
                $this->filter,
                $startDate['year'],
                $startDate['month'],
                $startDate['day'],
                $endDate['day']
            );
            return $sheets;
        }

        //make the first sheet only with start day
        $sheets[] = new EventsPerMonthSheet(
            $this->filter,
            $currentYear,
            $startDate['month'],
            $startDate['day'],
            (int)null
        );

        //loop the rest of the months until we reach the end month
        for ($i = $startDate['month'] + 1; $i <= 12; $i++) {
            //if we have reached the end month in the end year make the final sheet until the given day
            if ($i === $endDate['month'] && $currentYear === $endDate['year']) {
                $sheets[] = new EventsPerMonthSheet(
                    $this->filter,
                    $currentYear,
                    $i,
                    (int)null,
                    $endDate['day']
                );
                break;
            }

            //make a sheet for a whole month
            $sheets[] = new EventsPerMonthSheet(
                $this->filter,
                $currentYear,
                $i,
                (int)null,
                (int)null
            );

            //check if we have reached december
            if ($i === 12) {
                //if it's december, and it is the end year - break, if not, set month and increment the year
                if ($currentYear === $endDate['year']) {
                    break;
                }
                $i = 0;
                $currentYear++;
            }
        }

        return $sheets;
    }
}
