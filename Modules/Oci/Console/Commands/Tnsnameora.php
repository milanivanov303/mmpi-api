<?php

namespace Modules\Oci\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

/**
 * Generate API documentation
 *
 * @category Console_Command
 */
class Tnsnameora extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "tnsnameora:get-config";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Get tnsname config for oracle db connection";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $sftp = app('SFTP', ['host' => config('app.ssh.tnsname_host')]);
            $sftp->get(
                '/phpprod/services/src/opt/oracle/tns/tnsnames.ora',
                storage_path("app/tns/tnsnames.ora")
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
