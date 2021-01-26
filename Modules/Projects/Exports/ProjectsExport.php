<?php

namespace Modules\Projects\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProjectsExport implements
    FromCollection,
    WithMapping,
    WithHeadings,
    WithEvents,
    ShouldAutoSize
{
    use Exportable;

    public function __construct()
    {
        $this->collection = $this->projectModelCollection();
    }

    /*
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function projectModelCollection(): \Illuminate\Support\Collection
    {
        return DB::table('projects as p')
            ->join('enum_values as eva', 'eva.id', '=', 'p.activity')
            ->join('enum_values as evc', 'evc.id', '=', 'p.country_id')
            ->select(
                'p.id',
                'p.name',
                'eva.value as activity',
                'evc.value as country'
            )
            ->where('p.inactive', 0)
            ->orderBy('p.name', 'asc')
            ->get();

        // $projects = Project::with([
        //     'activityRel',
        //     'country'])
        //     ->where('inactive', 0)
        //     ->get();

        // return collect($projects);
    }

    public function map($projectInfo) : array
    {
        // $country  = $projectInfo->country->value ?? '';
        // $activity = $projectInfo->activity->value ?? '';
        return [
            $projectInfo->id,
            $projectInfo->name,
            $projectInfo->activity,
            $projectInfo->country,
        ];
    }

    public function headings() : array
    {
        return [
           '#',
           'Project',
           'Activity',
           'Country',
        ] ;
    }

    /**
     * Customizing columns in Excel table
     *
     * @return array
     */
    public function registerEvents() : array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:' . $event->sheet->getDelegate()->getHighestColumn() . '1'; // All affected headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray(
                    [
                        'font' => [
                            'name' => 'Ariel',
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
            },
        ];
    }
}
