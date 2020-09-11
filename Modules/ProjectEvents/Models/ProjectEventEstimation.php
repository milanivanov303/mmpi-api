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
use Modules\Hr\Services\HrService;

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
     * Get mail recipients
     * if the team doesn't have tl or dtl send the email to all users
     *
     * @param Collection $data
     * @return array
     */
    protected function getMailRecipients($data) : array
    {
        $roles = $data->userDepartmentRoles;
        if (count($roles) == 0) {
            $users = $data->users->toArray();
        } else {
            $users = ($roles->where('role_id', 'tl')
                ?? $roles->orWhere('role_id', 'dtl'))->pluck('user')->toArray();
        }

        $emails = array_filter(array_map(function ($user) {
            return $user['email'];
        }, $users), function ($email) {
            return $email === '' ? false : true;
        });

        return $emails;
    }

    /**
     * Set mail recipients
     *
     * @param ProjectEventEstimation $model
     * @return array
     */
    protected function setMailRecipients($model) : array
    {
        $userDepartmentRole   = $model->department;
        $userDepartmentEmails = $model->getMailRecipients($userDepartmentRole);
        $recipients['to']     =  $userDepartmentEmails;

        $notifyDepartments = $model
            ->projectEvent
            ->projectEventNotifications
            ->where('project_event_id', '=', $model->project_event_id);
        
        $departmentsEmails = [];
        foreach ($notifyDepartments as $notification) {
            $departmentsEmails[] = $model->getMailRecipients($notification->department);
        }

        if (!empty($departmentsEmails)) {
            $recipients['to'] = array_merge($recipients['to'], call_user_func_array('array_merge', $departmentsEmails));
        }

        $hr  = new HrService;
        $pmo = $hr->getProjectAvailablePmo($model->projectEvent->project->name);

        [$pmoTo] = $pmo;
        array_push($recipients['to'], $pmoTo->email);

        $recipients['cc'] = array_column($pmo, 'email');
        array_push($recipients['cc'], $model->madeBy->email);

        if ($model->projectEvent->projectEventType->value === "Assistance") {
            array_push($recipients['to'], config('mail.mailgroups.client_trainings'));
        }

        return $recipients;
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
            $recipients = $model->setMailRecipients($model);
            $message    = (new NewEstimationMail([
                'project' => $model->projectEvent->project->name,
                'user' => $model->madeBy->name,
                'department' => $model->department->name,
                'duration' =>  $model->duration
            ]))
            ->onQueue('mails');

            Mail::
                to($recipients['to'])
                ->cc($recipients['cc'])
                ->queue($message);
        });
    }
}
