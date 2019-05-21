<?php

namespace Modules\Certificates\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Certificates\Models\Certificate;
use Modules\Certificates\Services\CheckExpiryService;
use Modules\Certificates\Mail\CheckExpiryMail;
use App\Models\User;

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

        $date = Carbon::now()->subDays(30);

        $certificates = Certificate::whereDate('valid_to', '=', $date->format('Y-m-d'))->get();

        foreach ($certificates as $certificate) {
            $roles = $certificate->project->roles();

            $coordinators = $this->getProjectCoordinators($roles);
            $directors    = $this->getProjectDirectors($roles);

            if ($coordinators) {
                foreach ($coordinators as $coordinator) {
                    $user = $coordinator->user;

                    $data = $this->getData($user, $certificate);
                    $this->sendEmail($data);
                }
            }

            if ($directors) {
                $this->sendEmail($directors, $certificate);
            }
        }
    }

    /**
     * @param $roles
     * @return |null
     */
    protected function getProjectCoordinators($roles)
    {
        $coordinators = $roles->where('role_id', 'pc')->get();

        if ($coordinators) {
            return $coordinators;
        }

        return null;
    }

    /**
     * @param User $user
     * @param Certificate $certificate
     */
    protected function getData($user, $certificate)
    {
        $valideTo = Carbon::parse($certificate->valid_to)->format('Y-m-d');

        $data = [
                'user_name'    => $user->name,
                'project_name' => $certificate->project->name,
                'valid_to'     => $valideTo
                ];

        return $data;
    }

    protected function sendEmail($data)
    {
        Mail::to('eseimenova@codix.bg')->send(new CheckExpiryMail($data));
    }

    protected function getProjectDirectors($roles)
    {
        $directors = $roles->where('role_id', 'pm')->first();

        if ($directors) {
            return $directors;
        }

        $directors = $roles->where('role_id', 'dpm')->first();

        if ($directors) {
            return $directors->user;
        }

        return null;
    }
}
