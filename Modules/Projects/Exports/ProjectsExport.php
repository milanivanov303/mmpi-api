<?php

namespace Modules\Projects\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Projects\Models\Project;

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
        return Project::with([
                //'activity',
                'country',
                'intranetVersion',
                'roles.user'])
            ->where('inactive', 0)
            ->get();
    }

    public function map($projectInfo) : array
    {
        $country  = $projectInfo->country->value ?? '';
        $intranetVersion  = $projectInfo->intranetVersion->value ?? '';
        // Bad way but relation does not work, probably same name of relation/column
        $activity = $projectInfo->activity()->value('value') ?? '';
       
        $roles = [];
        foreach ($projectInfo->roles as $role) {
            $roles[$role['role_id']] = $role['user']['name'];
        }
        
        $pc = $roles['pc'] ?? '';
        $pm = $roles['pm'] ?? '';
        $dpc = $roles['dpc'] ?? '';
        $dpm = $roles['dpm'] ?? '';

        return [
            $projectInfo->name,
            $activity,
            $country,
            $intranetVersion,
            $pm,
            $dpm,
            $pc,
            $dpc
        ];
    }

    public function headings() : array
    {
        return [
           'Project',
           'Activity',
           'Country',
           'IMX PROD Version',
           'Project Manager',
           'Deputy Project Manager',
           'Project Cordinator',
           'Deputy Project Cordinator'
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
