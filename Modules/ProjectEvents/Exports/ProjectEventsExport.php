<?php

namespace Modules\ProjectEvents\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\ProjectEvents\Exports\Sheets\EventsPerMonthSheet;

class ProjectEventsExport implements WithMultipleSheets, Responsable
{
    use Exportable;

    private $year;
    private $fileName;

    /*
    * Export event constructor
    */
    public function __construct(int $year)
    {
        $this->year = $year;
        $this->fileName = "project_events_{$year}.xlsx";
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new EventsPerMonthSheet($this->year, $month);
        }
        return $sheets;
    }
}
