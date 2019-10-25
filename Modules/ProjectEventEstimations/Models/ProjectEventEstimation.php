<?php

namespace Modules\ProjectEventEstimations\Models;

use Core\Models\Model;
use Modules\Departments\Models\Department;
use Modules\ProjectEvents\Models\ProjectEvent;

class ProjectEventEstimation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_event_id',
        'department_id',
        'duration'
    ];

    // /**
    //  * The attributes that will be hidden in output json
    //  *
    //  * @var array
    //  */
    // protected $hidden = [
    //     'project_event_id',
    //     'id'
    // ];

    /**
     * Get project event
     */
    protected function projectEvent()
    {
        return $this->belongsTo(ProjectEvent::class, 'project_event_id');
    }

    /**
     * Get department
     */
    protected function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
