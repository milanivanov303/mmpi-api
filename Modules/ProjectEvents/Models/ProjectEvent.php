<?php

namespace Modules\ProjectEvents\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use App\Models\User;
use Modules\Projects\Models\Project;

class ProjectEvent extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'project',
        'madeBy',
        'projectEventType',
        'projectEventStatus'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'project_id',
        'project_event_type_id',
        'made_by',
        'project_event_status'
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
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get users
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by')->minimal();
    }

    /**
     * Get project event status
     */
    public function projectEventStatus()
    {
        return $this->belongsTo(EnumValue::class, 'project_event_status')->minimal();
    }

    /**
     * Get project event type
     */
    public function projectEventType()
    {
        return $this->belongsTo(EnumValue::class, 'project_event_type_id')->minimal();
    }
}
