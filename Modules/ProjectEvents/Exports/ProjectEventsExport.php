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

    private $request;
    private $fileName;

    /*
    * Export event constructor
    */
    public function __construct(Request $request)
    {
        $this->request = $request->all();
        $this->fileName = "project_events_{$this->request['year']}.xlsx";
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new EventsPerMonthSheet($this->request, $month);
        }
        return $sheets;
    }
}
