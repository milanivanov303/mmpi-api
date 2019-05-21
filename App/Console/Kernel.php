<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Core\Console\Commands\Openapi\Generate::class,
        \Modules\Users\Console\Commands\SynchronizeCommand::class,
        \Modules\Certificates\Console\Commands\CheckExpiry::class
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
            $schedule->command('users:synchronize')->dailyAt($time)->environments(['prod']);
        }
    }
}
