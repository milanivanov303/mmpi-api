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

    public function map($row) : array
    {
        $country  = $row->country->value ?? '';
        $intranetVersion  = $row->intranetVersion->value ?? '';
        // Bad way but relation does not work, probably same name of relation/column
        $activity = $row->activity()->value('value') ?? '';
       
        $roles = [];
        foreach ($row->roles as $role) {
            $roles[$role['role_id']][] = $role['user']['name'];
        }
        
        $pc = $roles['pc'] ?? '';
        if (is_array($pc)) {
            $pc=implode(', ', $pc);
        }

        $pm = $roles['pm'] ?? '';
        if (is_array($pm)) {
            $pm=implode(', ', $pm);
        }

        $dpc = $roles['dpc'] ?? '';
        if (is_array($dpc)) {
            $dpc=implode(', ', $dpc);
        }

        $dpm = $roles['dpm'] ?? '';
        if (is_array($dpm)) {
            $dpm=implode(', ', $dpm);
        }

        return [
            $row->name,
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
