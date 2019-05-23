<?php

namespace Modules\ProjectEvents\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use App\Models\User;
use Modules\Projects\Models\Project;

class ProjectEvent extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'project_id',
        'made_by',
        'project_event_status',
        'project_event_type_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'project_event_type_id',
        'event_start_date',
        'event_end_date',
        'made_by',
        'made_on',
        'description',
        'project_event_status'
    ];

    /**
     * Get projects
     */
    protected function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get users
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }

    /**
     * Get project event status
     */
    protected function projectEventStatus()
    {
        return $this->belongsTo(EnumValue::class, 'project_event_status');
    }

    /**
     * Get project event type
     */
    protected function projectEventType()
    {
        return $this->belongsTo(EnumValue::class, 'project_event_type_id');
    }
}
