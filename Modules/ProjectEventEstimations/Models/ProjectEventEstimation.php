<?php

namespace Modules\ProjectEventEstimations\Models;

use App\Models\Department;
use Core\Models\Model;
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
