<?php

namespace Modules\Modifications\Services;

use \Core\Helpers\SSH2;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;

class SeService
{
    /**
     * @var SSH2
     */
    protected $ssh2;

    /**
     * SE export type
     *
     * @var string
     */
    protected $type;

    /**
     * Client chain id
     *
     * @var int
     */
    protected $chain;

   /**
     * Instance user
     *
     * @var string
     */
    protected $user;

    /**
     * Export log file dir
     *
     * @var string
     */
    protected $logFileDir;

    /**
     * Export log
     *
     * @var string
     */
    protected $log = '';

    /**
     * Exported dmp for upload
     *
     * @var string
     */
    protected $seDump = '';

    /**
     * Export log time
     *
     * @var string
     */
    protected $logTime = '';

    /**
     * Export pid
     *
     * @var string
     */
    protected $pid = '';

    /**
     * Export exitCode
     *
     * @var string
     */
    protected $exitCode = '';

    /**
     * Export operation
     *
     * @var string
     */
    protected $operation = '';

    /**
     * Callback for status progress
     *
     * @var callable
     */
    protected $callback;

    /**
     * Current operations for automatic export
     *
     * @const
     */
    const VDNAM = 'se_vdnam';
    const TXTLIB = 'se_txt_lib';

    /**
     * ExportSeService constructor.
     *
     * @param SSH $ssh2
     * @param string $type
     * @param int $chain
     * @param callable $callback
     */
    public function __construct(
        SSH2 $ssh2,
        string $type,
        int $chain,
        string $user,
        callable $callback
    ) {
        $this->ssh2     = $ssh2;
        $this->type     = $type;
        $this->chain    = $chain;
        $this->user     = $user;
        $this->callback = $callback;
        $this->operation = EnumValue::find($type)->key;
    }

    /**
     * Download war file from build server
     * SE transfer - vd - ksh sh_cliimpbr vd nostart
     * @return bool
     */
    protected function start() : bool
    {
        $this->logTime = time();
        $this->logFileDir = $this->getWorkdir();
        $this->cleanHouse();

        $pid = $this->ssh2->exec(
            "export TERM=vt100; sudo su - {$this->user} -c '" . PHP_EOL
            . ". ~/.profile " . PHP_EOL
            . "nohup {$this->getCommandType()} > {$this->getLogFile()} 2>&1 &" . PHP_EOL
            . "echo $!'"
        );
        
        $this->pid = str_replace("\n", "", $pid);

        if ($this->ssh2->getExitStatus()) {
            $this->broadcast([
                'action'   => 'export',
                'status'   => 'failed',
                'comments' => 'Export has failed',
                'log'      =>  $this->ssh2->getStdError()
            ]);
            return false;
        }

        $this->broadcast([
            'action'   => 'export',
            'status'   => 'running',
            'comments' => 'Exporting...'
        ]);

        return true;
    }

    /**
     * Check export
     *
     * @return string
     */
    protected function check() : string
    {
        $export = $this->ssh2->exec("ps {$this->pid} | wc -l");
        
        $logStartLine = count(explode(PHP_EOL, $this->log));

        $log = $this->ssh2->exec("sed -n '{$logStartLine},\$p' < {$this->getLogFile()}");
        $this->log .= $log;


        $sanitized = preg_replace('/(?!\n)[\x00-\x1F\x7F\xA0]/u', ' ', $log);
        $sanitized = str_replace("[1m", "<b>", $sanitized);
        $sanitized = str_replace("[0m", "</b>", $sanitized);
        
        $status = $this->getStatus((int) $export);

        $message = [
            'action' => 'export',
            'status' => $status,
            'log'    => $sanitized
        ];

        if ($status !== 'running') {
            $message['comments'] = $status === 'exported' ? 'Export was completed successfully' : 'Export has failed';
        }

        $this->broadcast($message);

        return $status;
    }

    /**
     * Upload to Binary repo
     *
     * @return bool
     */
    protected function upload() : bool
    {
        $this->broadcast([
            'action'   => 'export.upload',
            'status'   => 'running',
            'comments' => 'Uploading dmp ...',
            'progress' => 0
        ]);

        $chain = DeliveryChain::find($this->chain);
        $clientRepo = str_replace(' ', '_', $chain->patch_directory_name);

        $artifact   = substr($this->seDump, strrpos($this->seDump, '/')+1)."\n";
        $artifactId = substr($artifact, 0, strpos($artifact, "."));

        $this->ssh2->exec(
            "export TERM=vt100; sudo su - {$this->user} -c '" . PHP_EOL
                . ". \${IMX_HOME}/extlib/profiles/.extlibprofile; cd {$this->logFileDir}" . PHP_EOL
                . "cp {$this->seDump} ./{$artifact}" . PHP_EOL
                // . "rm -rf {$clientRepo}" . PHP_EOL // risky got to find another way !
                . "hg pull" . PHP_EOL
                . "mvn -s ./settings.xml deploy -Des.client={$clientRepo} \
                    -Des.artifactId={$artifactId} -Des.version={$this->logTime}'"
        );

        if ($this->ssh2->getExitStatus()) {
            $this->broadcast([
                'action'   => 'export.upload',
                'status'   => 'failed',
                'comments' => 'Upload has failed',
                'error'    => $this->ssh2->getStdError()
            ]);
            return false;
        }

        $artifactPath = config('app.nexus.se_repo_url')
                        ."{$clientRepo}/{$artifactId}/{$this->logTime}/{$artifactId}-{$this->logTime}.tar.gz";

        $this->broadcast([
            'action'   => 'export.upload',
            'status'   => 'success',
            'comments' => 'Upload to Nexus successfully',
            'artifact' => $artifactPath,
            'progress' => 100
        ]);

        return true;
    }

    /**
     * Get export script by type
     *
     * @return string
     */
    protected function getCommandType() : string
    {
        $command = "";
        switch ($this->operation) {
            case self::VDNAM:
                $command = "sh_cliexpbr vdnam";
                $this->exitCode = "DUMP_FILE_FULL_PATH";
                break;
            case self::TXTLIB:
                $command = "se_text_db.sh exp";
                $this->exitCode = "iMX TEXT database exported into compressed format to";
                break;
            default:
                throw new \Exception("No execution script for this type is provided!");
        }

        return $command;
    }

    /**
     * Operation clean house
     *
     * @return void
     */
    protected function cleanHouse() : void
    {
        $dump = '';
        if ($this->operation === self::TXTLIB) {
            $dump = "/{$this->user}/intra/imx/base/textsbase.dmp";
            $this->seDump = "/{$this->user}/intra/imx/base/textsbase.dmp.Z";
        }

        if ($this->operation === self::VDNAM) {
            $dump = "/{$this->user}/intra/imx/base/client_vdnam.dmp";
            $this->seDump = "/{$this->user}/intra/imx/base/client_vdnam.dmp.Z";
        }

        $this->ssh2->exec(
            "export TERM=vt100; sudo su - {$this->user} -c '" . PHP_EOL
            . ". ~/.profile " . PHP_EOL
            . "rm -f {$this->seDump} {$dump}'"
        );
    }
    
    /**
     * Get log file
     *
     * @return string
     */
    protected function getLogFile() : string
    {
        $logName = str_replace(' ', '_', $this->getCommandType())."_".$this->logTime;
        return "{$this->logFileDir}/{$logName}.log";
    }

    /**
     * Get work dir
     *
     * @return string
     */
    protected function getWorkdir() : string
    {
        $seRepo  = config('app.nexus.rhode_url');
        $patches =  escapeshellarg('${IMX_PATCH}');

        $dir = $this->ssh2->exec(
            "export TERM=vt100; sudo su - {$this->user} -c '" . PHP_EOL
            . ". ~/.profile " . PHP_EOL
            . "[ -d '\${IMX_PATCH}/patch/system-expert' ] && echo '{$patches}/system-expert' || exit 1'"
        );

        if ($this->ssh2->getExitStatus()) {
            $dir = $this->ssh2->exec(
                "export TERM=vt100; sudo su - {$this->user} -c '" . PHP_EOL
                . ". ~/.profile " . PHP_EOL
                . "cd \${IMX_PATCH}" . PHP_EOL
                . "hg clone {$seRepo}" . PHP_EOL
                . "cd system-expert" . PHP_EOL
                . "pwd'"
            );

            $dir = array_filter(explode("\n", $dir));
            $dir = str_replace("\n", "", end($dir));

            if (strpos($dir, 'not found')) {
                $this->broadcast([
                    'action'   => 'export.clone',
                    'status'   => 'failed',
                    'comments' => 'Clone of repo has failed',
                    'log'      => $this->ssh2->getStdError()
                ]);
                throw new \Exception("Repo clone failed!");
            }
        }

        $dir = array_filter(explode("\n", $dir));
        $dir = str_replace("\n", "", end($dir));

        return $dir;
    }


    /**
     * Get export status
     *
     * @param int $running
     * @return string
     */
    protected function getStatus(int $running) : string
    {
        if ($running > 1) {
            return 'running';
        }

        $exitCode = "grep \"{$this->exitCode}\" {$this->getLogFile()}";
        $finished = $this->ssh2->exec($exitCode);
        if ($finished) {
            return 'exported';
        }
        
        return 'failed';
    }

    /**
     * Run build
     *
     * @return bool
     */
    public function run() : bool
    {
        $started = $this->start();

        if ($started) {
            while (true) {
                $status = $this->check();

                if ($status === 'exported') {
                    return $this->upload();
                }

                if ($status === 'failed') {
                    return false;
                }

                sleep(3);
            }
        }
        return false;
    }

    /**
     * Broadcast progress
     *
     * @param array $message
     */
    protected function broadcast(array $message)
    {
        ($this->callback)($message);
    }
}
