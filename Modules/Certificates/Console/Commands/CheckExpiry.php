<?php

namespace Modules\Certificates\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Certificates\Models\Certificate;
use Modules\Certificates\Mail\CheckExpiryMail;

/**
 * Generate API documentation
 *
 * @category Console_Command
 */
class CheckExpiry extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "certificates:check-expiry";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Check if there are certificates expiring soon";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::parse(config('app.certificates.check_expiry'))->format('Y-m-d');
        $certificates = Certificate::whereDate('valid_to', '=', $date)->get();

        foreach ($certificates as $certificate) {
            $roles = $certificate->project->roles();

            $coordinators = $this->getProjectCoordinators(clone $roles);
            $directors    = $this->getProjectDirectors(clone $roles);

            $recipients = $this->getRecipients($coordinators, $directors);
            $message = $this->getMessage($certificate);

            Mail::to($recipients['to'])->cc($recipients['cc'])->send(new CheckExpiryMail($message));
        }
    }

    /**
     * @param array $coordinators
     * @param array $directors
     */
    protected function getRecipients($coordinators, $directors)
    {
        // Set recipients
        $to = $coordinators ? $coordinators : $directors;
        $cc = $coordinators ? $directors : [];

        $data = [
            'to' => $to,
            'cc' => $cc
        ];

        return $data;
    }

    /**
     * @param Certificate $certificate
     */
    protected function getMessage($certificate)
    {
        $valideTo = Carbon::parse($certificate->valid_to)->format('Y-m-d');

        $data = [
            'project_name' => $certificate->project->name,
            'valid_to'     => $valideTo
        ];

        return $data;
    }

    /**
     * Get project coordinators
     * @param $roles
     * @return array|null
     */
    protected function getProjectCoordinators($roles)
    {
        $coordinators = $roles->where('role_id', 'pc')->get()->pluck('user')->toArray();

        if ($coordinators) {
            return $coordinators;
        }

        return null;
    }

    /**
     * Get project directors
     * @param $roles
     * @return array|null
     */
    protected function getProjectDirectors($roles)
    {
        $directors = $roles->where('role_id', 'pm')->get()->pluck('user')->toArray();

        if ($directors) {
            return $directors;
        }

        return null;
    }
}
