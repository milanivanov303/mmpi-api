<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Delete records from table access_trace older than 6 months
 *
 * @category Console_Command
 */
class ClearAccessTraceCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "clear:access-trace";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Delete records older than 6 months";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $time = Carbon::now()->subMonths(6)->format('Y-m-d H:i:s');

        $deleted = app(DB::class)
            ::table('access_trace')
            ->where('access_time', '<', $time)
            ->delete();

        $this->info("{$deleted} records deleted");
    }
}
