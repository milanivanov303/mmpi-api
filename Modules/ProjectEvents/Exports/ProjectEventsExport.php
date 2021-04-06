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

    //filter for excel export file
    private $filter;
    //name of the exported file
    private $fileName;

    /*
    * Export event constructor
    */
    public function __construct(Request $request)
    {
        $this->filter = $request->all();
        $this->fileName = "project_events_{$this->filter['year']}.xlsx";
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new EventsPerMonthSheet($this->filter, $month);
        }
        return $sheets;
    }
}
