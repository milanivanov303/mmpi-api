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

            Mail::
                to($this->getTo($coordinators, $directors))
                ->cc($this->getCc($coordinators, $directors))
                ->send(new CheckExpiryMail($this->getData($certificate)));
        }
    }

    /**
     * @param array $coordinators
     * @param array $directors
     * @return array
     */
    protected function getTo($coordinators, $directors) : array
    {
        if ($coordinators) {
            return $coordinators;
        }

        if ($directors) {
            return $directors;
        }

        return [];
    }

    /**
     * @param array $coordinators
     * @param array $directors
     * @return array
     */
    protected function getCc($coordinators, $directors) : array
    {
        if ($coordinators && $directors) {
            return $directors;
        }

        return [];
    }

    /**
     * @param Certificate $certificate
     * @return array
     */
    protected function getData($certificate) : array
    {
        $valideTo = Carbon::parse($certificate->valid_to)->format('Y-m-d');

        return [
            'project_name' => $certificate->project->name,
            'valid_to'     => $valideTo,
            'hash'         => $certificate->hash
        ];
    }

    /**
     * Get project coordinators
     * @param $roles
     * @return array|null
     */
    protected function getProjectCoordinators($roles) : ?array
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
    protected function getProjectDirectors($roles) : ?array
    {
        $directors = $roles->where('role_id', 'pm')->get()->pluck('user')->toArray();

        if ($directors) {
            return $directors;
        }

        return null;
    }
}
