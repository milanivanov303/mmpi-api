<?php

namespace Modules\ProjectEvents\Models;

use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Core\Models\Model;
use Illuminate\Support\Facades\Auth;
use Modules\ProjectEvents\Models\ProjectEvent;

class ProjectEventNotifications extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_event_id',
        'department_id',
        'made_by',
        'made_on'
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

    /**
     * Get user
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->madeBy()->associate(Auth::user());
            $model->made_on = Carbon::now()->format('Y-m-d H:i:s');
        });
    }
}
