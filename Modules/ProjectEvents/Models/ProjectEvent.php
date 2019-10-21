<?php

namespace Modules\ProjectEvents\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use App\Models\User;
use Modules\Projects\Models\Project;
use Modules\PojectEventEstimations\Models\PojectEventEstimation;

class ProjectEvent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'project_event_type_id',
        'project_event_subtype_id',
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

    /**
     * Get project event subtype
     */
    protected function projectEventSubtype()
    {
        return $this->belongsTo(EnumValue::class, 'project_event_subtype_id');
    }

    /**
     * Get project event estimations
     */
    protected function pojectEventEstimations()
    {
        return $this->hasMany(PojectEventEstimation::class);
    }
}
