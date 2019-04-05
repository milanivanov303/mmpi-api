<?php

namespace Modules\Certificates\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Certificates\Models\Certificate;

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

        $date = Carbon::now()
            ->addYears(2)
            ->addMonth(6)
            ->addDays(30);

        $certificates = Certificate::where('valid_to', '<', $date->format('Y-m-d'))->get();

        foreach ($certificates as $certificate) {
            $roles = $certificate->project->roles();

            $coordinator = $this->getProjectCoordinator($roles);
            $director    = $this->getProjectDirector($roles);

            if ($coordinator) {
                //send mail
            }

            if ($director) {
                //send mail
            }
        }
    }

    /**
     * @param $roles
     * @return |null
     */
    protected function getProjectCoordinator($roles)
    {
        $coordinator = $roles->where('role_id', 'pc')->first();

        if ($coordinator) {
            return $coordinator->user;
        }

        return null;
    }

    protected function getProjectDirector($roles)
    {
        $director = $roles->where('role_id', 'pm')->first();

        if ($director) {
            return $director->user;
        }

        $director = $roles->where('role_id', 'dpm')->first();

        if ($director) {
            return $director->user;
        }

        return null;
    }
}
