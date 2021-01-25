<?php

namespace Modules\Projects\Exports;

use Modules\Projects\Models\Project;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProjectsExport implements
    FromCollection,
    WithMapping,
    WithHeadings,
    ShouldAutoSize
{
    use Exportable;


    // public function collection()
    // {
    //     return Project::all();
    // }
   /*
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Project::with([              
            'activity'
            ])
            ->where('inactive', '=', 0)
            ->get();
    }

    public function map($projectInfo) : array
    {
        $country  = $projectInfo->country->value ?? '';
        $activity = $projectInfo->activity->value ?? '';
        return [
            $projectInfo->name,
            $activity,
            $country
        ] ;
       
    }

    public function headings() : array
    {
        return [
           'Name',
           'Activity',
           'Country',
           'test',
        ];
    }
}
