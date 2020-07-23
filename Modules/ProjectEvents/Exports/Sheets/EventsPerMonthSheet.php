<?php

namespace Modules\ProjectEvents\Exports\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\ProjectEvents\Models\ProjectEvent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class EventsPerMonthSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithEvents
    //ShouldAutoSize
{
    private $month;
    private $monthName;
    private $year;
    private $collection;

    public function __construct(int $year, int $month)
    {
        $this->month      = $month;
        $this->year       = $year;
        $this->collection = $this->constructCal();
    }

   /*
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->collection;
    }

    private function constructCal() : \Illuminate\Support\Collection
    {
        $start = Carbon::parse("{$this->year}-{$this->month}")->startOfMonth();
        $end = Carbon::parse("{$this->year}-{$this->month}")->endOfMonth();
       
        $dates = [];
        while ($start->lte($end)) {
            $carbon = Carbon::parse($start);
            $dates[$carbon->format("W")][$carbon->format("l")] = ltrim($start->copy()->format('d'), '0');
            $start->addDay();
        }

        $headings = [
            'Monday' => '',
            'Tuesday' => '',
            'Wednesday' => '',
            'Thursday' => '',
            'Friday' => '',
            'Saturday' => '',
            'Sunday' => ''
        ];

        $monthEvents = ProjectEvent::with([
            'project',
            'madeBy',
            'projectEventStatus',
            'projectEventType',
            'projectEventSubtype',
            'projectEventEstimations'])
            ->whereYear('event_end_date', $this->year)
            ->whereMonth('event_end_date', $this->month)
            ->get();

        foreach ($dates as $week => $date) {
            $dates[$week] = array_merge($headings, $date);
            foreach ($monthEvents as $event) {
                $day = Carbon::parse($event->event_end_date)->format('l');
                $subType = $event->projectEventSubtype->value ?? '';
                if (Carbon::parse($event->event_end_date)->format('d') === $dates[$week][$day]) {
                    $dates[$week][$day] = "{$date[$day]}\n{$event->project->name}
                        -{$event->projectEventType->value}/{$subType}";
                }
            }
            // foreach ($monthEvents as $event) {
            //     $day = Carbon::parse($event->event_end_date)->format('l');
            //     // $subType = $event->projectEventSubtype->value ?? '';
            //     if (Carbon::parse($event->event_end_date)->format('d') === $dates[$week][$day]) {
            //         $dayEvents[$day][] = "{$event->project}:{$event->projectEventType->value}\n";
            //     }
            //     $dayEvents[$day] = '';
            // }
            // foreach ($date as $day) {
            //     if (isset($dayEvents[$day])) {
            //         $toString = implode("\n", $dayEvents[$day]);
            //         $dates[$week][$day] = "{$day}\n{$toString}";
            //     }
            // }
        }
        return collect($dates);
        // $subType = $event->projectEventSubtype->value ?? '';
        // return [
        //     $event->project->name,
        //     $event->projectEventType->value,
        //     $subType,
        //     Carbon::parse($event->event_start_date)->toFormattedDateString(),
        //     Carbon::parse($event->event_end_date)->toFormattedDateString(),
        //     $event->madeBy->name,
        //     $event->description,
        // ] ;
    }

    public function headings() : array
    {
        return [
            [$this->monthName],
            [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            ]
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
                $cellRangeData    = 'A3:G8';
                $event->sheet->columnWidthDefault(20);
                $event->sheet->rowHeightDefault(40);
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->styleCells('A1:G1', $this->headerStyles('FF00BFFF'));
                $event->sheet->styleCells('A2:G2', $this->headerStyles('FF800000'));
                $event->sheet->styleCells(
                    $cellRangeData,
                    [
                        'font' => [
                            'bold' => false,
                            'color' => ['argb' => 'FF00BFFF'],
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FF00BFFF'],
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
        $this->monthName = $date->format('F');
        return $this->monthName;
    }

    /**
     * @return array
     */
    private function headerStyles($color): array
    {
        return [
            'font' => [
                'size' => 15,
                'bold' => true,
                'color' => ['argb' => 'FFFFAF0'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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
                    'argb' => 'FF87CEFA',
                ],
                'endColor' => [
                    'argb' => $color,
                ],
            ]
        ];
    }
}
