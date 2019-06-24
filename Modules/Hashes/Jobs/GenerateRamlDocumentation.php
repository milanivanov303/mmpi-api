<?php

namespace Modules\Hashes\Jobs;

use Core\Helpers\SSH2;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class GenerateRamlDocumentation implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $hashRev;

    /**
     * @var array
     */
    protected $ramlFiles;

    /**
     * Create a new job instance.
     *
     * @param string $hashRev
     * @param array $ramlFiles
     * @return void
     */
    public function __construct(string $hashRev, Collection $ramlFiles)
    {
        $this->hashRev   = $hashRev;
        $this->ramlFiles = $ramlFiles;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Log::info("Generate documentation for hash '{$this->hashRev}'");

        $host     = config('app.raml2html.host');
        $username = config('app.raml2html.username');
        $password = config('app.raml2html.password');

        $ssh2 = new SSH2($host);

        // login to server
        if (!$ssh2->login($username, $password)) {
            throw new \Exception("Could login to {$host}");
        }

        $cmd = "
            . .profile > /dev/null 2>&1 \
            && cd /enterprise/src/raml2htmlgen \
            && php ./index.php --raml2html {$this->hashRev}
        ";

        Log::info("Run '{$cmd}'");

        $output = $ssh2->exec($cmd);

        Log::info("Output '{$output}'");
    }
}
