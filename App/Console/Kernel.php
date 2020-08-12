<?php

namespace App\Console;

use App\Console\Commands\HeadMergeCommand;
use Core\Console\Commands\Openapi\Generate as OpenapiGenerate;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Modules\Certificates\Console\Commands\CheckExpiry;
use Modules\Users\Console\Commands\SynchronizeCommand;
use Modules\Oci\Console\Commands\Tnsnameora;
use Modules\Hashes\Console\Commands\HashesSynchronizeCommand;
use Modules\ProjectEvents\Console\Commands\ProjectEventsArchive;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        OpenapiGenerate::class,
        SynchronizeCommand::class,
        CheckExpiry::class,
        HeadMergeCommand::class,
        Tnsnameora::class,
        HashesSynchronizeCommand::class,
        ProjectEventsArchive::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        foreach (['8:55', '10:55', '13:55', '18:55'] as $time) {
            $schedule->command('users:synchronize')
                ->dailyAt($time)
                ->environments(['prod']);
        }

        $schedule->command('certificates:check-expiry')
            ->dailyAt('10:00')
            ->environments(['prod']);

        $schedule->command('sources:head-merge')
            ->dailyAt('02:00')
            ->environments(['prod'])
            ->appendOutputTo(storage_path("logs/head-merge-command.log"));

        $schedule->command('tnsnameora:get-config')
            ->dailyAt('10:00')
            ->environments(['prod']);

        $schedule->command('project-events:archive')
            ->everyFifteenMinutes()
            ->environments(['prod']);

        $schedule->command('hashes:synchronize');
    }
}
