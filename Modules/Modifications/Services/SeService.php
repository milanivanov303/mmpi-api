<?php

namespace Modules\Modifications\Services;

use Carbon\Carbon;
use \Core\Helpers\SSH2;
use App\Models\EnumValue;

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
     * Client chain name
     *
     * @var string
     */
    protected $chain;

    /**
     * Export log file dir
     *
     * @var string
     */
    protected $logFileDir = '../patches/se_repo';

    /**
     * Export log
     *
     * @var string
     */
    protected $log = '';

    /**
     * Callback for status progress
     *
     * @var callable
     */
    protected $callback;

    /**
     * ExportSeService constructor.
     *
     * @param SSH $ssh2
     * @param string $type
     * @param string $chain
     * @param callable $callback
     */
    public function __construct(
        SSH2 $ssh2,
        string $type,
        // string $chain,
        callable $callback
    ) {
        $this->ssh2 = $ssh2;
        $this->type = $type;
        // $this->chain    = $chain;
        $this->callback = $callback;
    }

    /**
     * Download war file from build server
     * SE transfer - vd - ksh sh_cliimpbr vd nostart
     * @return bool
     */
    protected function start() : bool
    {
        $cmd = "export TERM=vt100; sudo su - bira -c "
            ."'"
                . "export TERM=vt100 ; . ~/.profile ;"
                . "nohup {$this->getCommandType()} > {$this->getLogFile()} 2>&1;"
            ."'";

        $this->ssh2->exec($cmd);
        
        if ($this->ssh2->getExitStatus()) {
            $this->broadcast([
                'action' => 'export',
                'status' => 'failed',
                'summary' => 'Export has failed',
                'log' =>  $cmd . PHP_EOL . $this->ssh2->getStdError()
            ]);
            return false;
        }

        $this->broadcast([
            'action' => 'export',
            'status' => 'running',
            'summary' => 'Exporting...'
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
        $psCmd   = "ps -fea | grep {$this->getCommandType()} | wc -l";
        $running = $this->ssh2->exec($psCmd);

        $logStartLine = count(explode(PHP_EOL, $this->log));

        $sedCmd = "sed -n '{$logStartLine},\$p' < {$this->getLogFile()}";
        $log    = $this->ssh2->exec($sedCmd);

        $this->log .= $log;

        $status = $this->getStatus((int) $running);

        $message = [
            'action' => 'export',
            'status' => $status,
            'comments' => $log
        ];

        if ($status !== 'running') {
            $message['summary'] = $status === 'success' ? 'Export was completed successfully' : 'Export has failed';
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
            'action' => 'export.upload',
            'status' => 'running',
            'summary' => 'Uploading dmp ...',
            'progress' => 0
        ]);

        $nexus = "cd {$this->getLogFile()}"
            . "mvn deploy -Des.client=FIRS_translations -Des.artifactId=client_vdpar -Des.version=1.0.2";

        $upload = $this->ssh2->exec($nexus);

        if (!$upload) {
            $this->broadcast([
                'action'   => 'export.upload',
                'status'   => 'failed',
                'summary'  => 'Upload has failed',
                'comments' => $this->ssh2->getStdError()
            ]);
            return false;
        }

        $this->broadcast([
            'action'   => 'export.upload',
            'status'   => 'success',
            'summary' => 'Upload to Nexus successfully',
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
        $typeInfo = EnumValue::find($this->type);

        $command = '';
        
        switch ($typeInfo->key) {
            case 'se_vd':
                $command = 'ksh sh_cliimpbr vd';
                break;
            default:
                throw new \Exception("No execution script for this type is provided!");
        }

        return $command;
    }

    /**
     * Get log file
     *
     * @return string
     */
    protected function getLogFile() : string
    {
        $logName = str_replace(' ', '_', $this->getCommandType())."_".time();
        return "{$this->logFileDir}/{$logName}.log";
    }

    /**
     * Get build status
     *
     * @param int $running
     * @return string
     */
    protected function getStatus(int $running) : string
    {
        if ($running > 1) {
            return 'running';
        }

        $exitCode = "grep \"Exit code: 0\" {$this->getLogFile()}";
        $finished = $this->ssh2->exec($exitCode);
        if ($finished) {
            return 'success';
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

                if ($status === 'success') {
                    //return $this->upload();
                }

                if ($status === 'failed') {
                    return false;
                }

                sleep(2);
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
