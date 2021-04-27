<?php

namespace Modules\Modifications\Services;

use Core\Helpers\SSH2;
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
     * Contents string for BTPROC and BTTEXT
     *
     * @var string
     */
    protected $contents;

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
    const BTPROC = 'bkg_trans_proc';
    const BTTEXT = 'bkg_trans_texte';

    /**
     * ExportSeService constructor.
     *
     * @param SSH2 $ssh2
     * @param string $type
     * @param int $chain
     * @param callable $callback
     */
    public function __construct(
        SSH2 $ssh2,
        string $type,
        int $chain,
        string $user,
        string $contents,
        callable $callback
    ) {
        $this->ssh2     = $ssh2;
        $this->type     = $type;
        $this->chain    = $chain;
        $this->user     = $user;
        $this->contents = $contents;
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

        $cmd = ". ~/.profile" . PHP_EOL
            . "nohup {$this->getCommandType()} > {$this->getLogFile()} 2>&1 &" . PHP_EOL
            . "echo $!";

        $pid = $this->ssh2->execAs(
            $this->user,
            $cmd
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

        $cmd = ". \${IMX_HOME}/extlib/profiles/.extlibprofile; cd {$this->logFileDir}" . PHP_EOL
            . "cp {$this->seDump} ./{$artifact}" . PHP_EOL
            // . "rm -rf {$clientRepo}" . PHP_EOL // risky got to find another way !
            . "hg pull" . PHP_EOL
            . "mvn -s ./settings.xml deploy -Des.client={$clientRepo} \
                -Des.artifactId={$artifactId} -Des.version={$this->logTime}";

        $this->ssh2->execAs(
            $this->user,
            $cmd
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
            'version'  => $this->logTime,
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
        switch ($this->operation) {
            case self::VDNAM:
                $command = "sh_cliexpbr vdnam";
                $this->exitCode = "Exit code:.*0";
                break;
            case self::TXTLIB:
                $command = "se_text_db.sh exp";
                $this->exitCode = "iMX TEXT database exported into compressed format to";
                break;
            case self::BTPROC:
                $command = "bkg_trans_proc {$this->contents} -f {$this->seDump} exp";
                $this->exitCode = "Export terminated successfully without warnings.";
                break;
            case self::BTTEXT:
                $command = "bkg_trans_texte -f {$this->seDump} exp \"{$this->contents}\"";
                $this->exitCode = "Export terminated successfully without warnings.";
                break;
            default:
                throw new \Exception("No execution script for this type is provided!");
        }

        return $command;
    }

    /**
     * Operation clean house
     *bkg_trans_proc CFPM -f /bull/intra/imx/patch/system-expert/ES_CFPM_20200403123514.dmp exp
     * @return void
     */
    protected function cleanHouse() : void
    {
        switch ($this->operation) {
            case self::VDNAM:
                $dump = "/{$this->user}/intra/imx/base/client_vdnam.dmp";
                $this->seDump = "/{$this->user}/intra/imx/base/client_vdnam.dmp.Z";
                $this->cleanExec($dump);
                break;
            case self::TXTLIB:
                $dump = "/{$this->user}/intra/imx/base/textsbase.dmp";
                $this->seDump = "/{$this->user}/intra/imx/base/textsbase.dmp.Z";
                $this->cleanExec($dump);
                break;
            case self::BTPROC:
                $dump = '';
                $this->seDump = "\$IMX_TMP/ES_{$this->contents}.dmp";
                $this->cleanExec($dump);
                break;
            case self::BTTEXT:
                $dump = '';
                $this->seDump = "\$IMX_TMP/LETTERS.dmp";
                $this->cleanExec($dump);
                break;
        }
    }

    /**
     * Clean exec
     *
     * @return void
     */
    protected function cleanExec($dump) : void
    {
        $cmd = ". ~/.profile" . PHP_EOL
            . "rm -f {$this->seDump} {$dump}";

        $this->ssh2->execAs(
            $this->user,
            $cmd
        );
    }

    /**
     * Get log file
     *
     * @return string
     */
    protected function getLogFile() : string
    {
        list($logName) = explode(' ', $this->getCommandType());
        $logName = $logName."_".$this->logTime;
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
        $repoDir =  "/{$this->user}/intra/imx/patch/system-expert";
        $cmd = ". ~/.profile" . PHP_EOL
            . "[ -d {$repoDir} ] && echo {$repoDir} || exit 1";

        $dir = $this->ssh2->execAs(
            $this->user,
            $cmd
        );

        if ($this->ssh2->getExitStatus()) {
            $cmd = ". \${IMX_HOME}/extlib/profiles/.extlibprofile" . PHP_EOL
                . "cd /{$this->user}/intra/imx/patch" . PHP_EOL
                . "hg clone {$seRepo}" . PHP_EOL
                . "cd system-expert" . PHP_EOL
                . "pwd";

            $dir = $this->ssh2->execAs(
                $this->user,
                $cmd
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
