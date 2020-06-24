<?php

namespace Modules\InstanceDowntimes\Services;

use Modules\Hr\Services\HrService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Modules\InstanceDowntimes\Models\InstanceDowntime;
use Modules\InstanceDowntimes\Mail\InstanceDowntimeMail;

class NotificationService
{
    /**
     * Data stack
     *
     * @var array
     */
    protected $data;

    /**
     * NotificationService constructor.
     *
     * @param InstanceDowntime $model
     * @param array $data
     */
    public function __construct(InstanceDowntime $model, array $data)
    {
        $data['start_datetime'] = $data['start_datetime'] ?? $model->start_datetime;
        $data['end_datetime']   = $data['end_datetime'] ?? $model->end_datetime;
        $data['description']    = $data['description'] ?? $model->description;
        $data['instance']       = $model->instance->name;
        $data['timezone']       = $model->instance->timezone;
        $data['chain']          = $model->instance->deliveryChains->first();
        $data['template']       = "mails.instance-downtime";
        
        $this->data = $data;
    }

    /**
     * Send notification
     *
     */
    public function sendNotification()
    {
        $senderEmail = Auth::user()->email ?? config('mail.mailgroups.installer');

        $this->data['from'] = $senderEmail;
        $this->data['to']   = config('mail.mailgroups.codix');
        $this->data['cc']   = "";

        if (!is_null($this->data['chain'])) {
            $this->data['project'] = $this->data['chain']->projects->first()->name;

            $pmo = app(HrService::class)->getProjectAvailablePmo($this->data['project']);
    
            [$this->data['to']] = $pmo;
            $this->data['cc']   = array_column($pmo, 'email');
            array_push($this->data['cc'], $senderEmail);
        }


        if ($this->data['status'] === 0) {
            $this->data['template'] = "mails.instance-downtime-archive";
        }

        try {
            Mail::queue((new InstanceDowntimeMail($this->data))->onQueue('mails'));
            Log::info("{$this->data['instance']} Downtime Mail sended");
        } catch (\Exception $e) {
            Log::error("{$this->data['instance']} Downtime Mail ERR: {$e->getMessage()}");
        }
    }
}
