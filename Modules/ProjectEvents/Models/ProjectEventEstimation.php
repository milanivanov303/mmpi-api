<?php

namespace Modules\ProjectEvents\Models;

use Core\Models\Model;

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
}
