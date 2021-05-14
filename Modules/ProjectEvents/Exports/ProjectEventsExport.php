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
        $this->fileName = "project_events_{$this->filter['year']}.xlsx";
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        if ($this->filter['month'] != null) {
            $sheets[] = new EventsPerMonthSheet($this->filter, $this->filter['month']);
            return $sheets;
        }
        
        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new EventsPerMonthSheet($this->filter, $month);
        }
        return $sheets;
    }
}
