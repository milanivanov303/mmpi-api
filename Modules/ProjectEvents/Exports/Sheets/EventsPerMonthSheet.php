<?php

namespace Modules\ProjectEvents\Exports\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\ProjectEvents\Models\ProjectEvent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class EventsPerMonthSheet implements
    FromCollection,
    WithTitle,
    WithMapping,
    WithHeadings,
    WithEvents,
    ShouldAutoSize
{
    private $month;
    private $year;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;
        $this->year  = $year;
    }

   /*
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProjectEvent::with([
          'project',
          'madeBy',
          'projectEventStatus',
          'projectEventType',
          'projectEventSubtype',
          'projectEventEstimations'])
          ->whereYear('event_end_date', $this->year)
          ->whereMonth('event_end_date', $this->month)
          ->get();
    }

    public function map($event) : array
    {
        $subType = $event->projectEventSubtype->value ?? '';
        return [
            $event->project->name,
            $event->projectEventType->value,
            $subType,
            Carbon::parse($event->event_start_date)->toFormattedDateString(),
            Carbon::parse($event->event_end_date)->toFormattedDateString(),
            $event->madeBy->name,
            $event->description,
            $event->projectEventStatus->value,
        ] ;
    }

    public function headings() : array
    {
        return [
           'Project',
           'Event Type',
           'Event Sub Type',
           'Start Date',
           'End Date',
           'Made By',
           'Description',
           'Status'
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class  => function (BeforeExport $event) {
                $event->writer->setCreator('MMPI API');
            },
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:H1';
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->styleCells(
                    $cellRange,
                    [
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => 'FFA0A0A0',
                            ],
                            'endColor' => [
                                'argb' => 'FFFFFFFF',
                            ],
                        ],
                    ]
                );
            },
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $date  = \DateTime::createFromFormat('!m', $this->month);
        $month = $date->format('F');
        return $month;
    }
}
