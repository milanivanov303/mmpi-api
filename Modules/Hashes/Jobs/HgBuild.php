<?php

namespace Modules\Hashes\Jobs;

use Carbon\Carbon;
use Core\Helpers\SSH2;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HgBuild implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $hashRev;

    /**
     * Create a new job instance.
     *
     * @param string $hashRev
     * @return void
     */
    public function __construct(string $hashRev)
    {
        $this->hashRev = $hashRev;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Log::info("Start build for hash '{$this->hashRev}'");

        $host     = config('app.hg-build.host');
        $username = config('app.hg-build.username');
        $password = config('app.hg-build.password');

        $ssh2 = new SSH2($host);

        // login to server
        if (!$ssh2->login($username, $password)) {
            throw new \Exception("Could login to {$host}");
        }

        $logFile = '${HOME}/trace/hg_build_' . $this->hashRev . '.log';

        $cmd = "
            . .profile > /dev/null 2>&1; \
            nohup shell/hg_build.sh {$this->hashRev}  > {$logFile} 2>&1 &
        ";

        Log::info("Run '{$cmd}'");

        $ssh2->exec($cmd);
    }
}
