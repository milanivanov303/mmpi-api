<?php

namespace Modules\ProjectEvents\Models;

use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Core\Models\Model;
use Illuminate\Support\Facades\Auth;
use Modules\ProjectEvents\Models\ProjectEvent;
use Illuminate\Support\Facades\Mail;
use Modules\ProjectEvents\Mail\NewEstimationMail;

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
        'duration',
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

        static::created(function ($model) {
            $userDepartmentRole = $model
                ->department
                ->userDepartmentRoles;

            // if the team doesn't have tl or dtl send the email to all users
            if (count($userDepartmentRole) == 0) {
                $users = $model->department->users->toArray();
            } else {
                $users = ($userDepartmentRole->where('role_id', 'tl')
                        ?? $userDepartmentRole->orWhere('role_id', 'dtl'))->pluck('user')->toArray();
            }

            $to = array_filter(array_map(function ($user) {
                return $user['email'];
            }, $users), function ($email) {
                return $email === '' ? false : true;
            });
            array_push($to, config('mail.mailgroups.client_trainings'));
            $cc = $model->madeBy->email;

            Mail::
                to($to)
                ->cc($cc)
                ->send(new NewEstimationMail([
                    'project' => $model->projectEvent->project->name,
                    'user' => $model->madeBy->name,
                    'department' => $model->department->name,
                    'duration' =>  $model->duration
                ]));
        });
    }
}
