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

            $data = $this->getEmailData($coordinators, $directors, $certificate);

            Mail::send(new CheckExpiryMail($data));
        }
    }

    /**
     * @param array $coordinators
     * @param array $directors
     * @param Certificate $certificate
     */
    protected function getEmailData($coordinators, $directors, $certificate)
    {
        $valideTo = Carbon::parse($certificate->valid_to)->format('Y-m-d');

        // Set recipients
        $to = $coordinators ? $coordinators : $directors;
        $cc = $coordinators ? $directors : null;

        $data = [
            'recipients' => [
                'to' => $to,
                'cc' => $cc
            ],
            'message' => [
                'project_name' => $certificate->project->name,
                'valid_to'     => $valideTo
            ]
        ];

        return $data;
    }

    /**
     * Get emails of project coordinators
     * @param $roles
     * @return array|null
     */
    protected function getProjectCoordinators($roles)
    {
        $coordinators = $roles->where('role_id', 'pc')->get()->pluck('user')->pluck('email')->toArray();

        if ($coordinators) {
            return $coordinators;
        }

        return null;
    }

    /**
     * Get emails of project directors
     * @param $roles
     * @return array|null
     */
    protected function getProjectDirectors($roles)
    {
        $directors = $roles->where('role_id', 'pm')->get()->pluck('user')->pluck('email')->toArray();

        if ($directors) {
            return $directors;
        }

        return null;
    }
}
