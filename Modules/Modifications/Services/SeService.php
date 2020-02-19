<?php

namespace Modules\Modifications\Services;

use Carbon\Carbon;
use \Core\Helpers\SSH2;

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
     * Exported dmp version
     *
     * @var int
     */
    protected $version;

    /**
     * Export log file dir
     *
     * @var string
     */
    protected $logFileDir = '/bira/intra/imx/patches/se_repo';

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
     * @param string $version
     * @param string $chain
     * @param callable $callback
     */
    public function __construct(
        SSH2 $ssh2,
        // int $version,
        // string $type,
        // string $chain,
        callable $callback
    ) {
        
        $this->ssh2      = $ssh2;
        // $this->version  = $version;
        // $this->type     = $type;
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
                . "nohup ls -l > {$this->getLogFile()} 2>&1;"
            ."'";

        $this->ssh2->exec($cmd);
        
        // if ($this->sftp->getExitStatus()) {
        //     $this->broadcast([
        //         'action' => 'build',
        //         'status' => 'failed',
        //         'summary' => 'Build has failed',
        //         'log' =>  $cmd . PHP_EOL . $this->sftp->getStdError()
        //     ]);
        //     return false;
        // }

        $this->broadcast([
            'action' => 'build',
            'status' => 'running',
            'summary' => 'Build is running ...',
            'comment' => $cmd . PHP_EOL
        ]);

        return true;
    }

    /**
     * Check build
     *
     * @return string
     */
    protected function check() : string
    {
        $cmd1    = "ps -fea | grep build-extranet-hg.sh | grep {$this->branch} | grep {$this->createdBy} | wc -l";
        $running = $this->sftp->exec($cmd1);

        $logStartLine = count(explode(PHP_EOL, $this->log));

        $cmd2 = "sed -n '{$logStartLine},\$p' < {$this->getLogFile()}";
        $log  = $this->sftp->exec($cmd2);

        $this->log .= $log;

        $status = $this->getStatus((int) $running);

        $message = [
            'action' => 'build',
            'status' => $status,
            'log' => $log
        ];

        if ($status !== 'running') {
            $message['summary'] = $status === 'success' ? 'Build was completed successfully' : 'Build has failed';
        }

        $this->broadcast($message);

        return $status;
    }

    /**
     * Get log file
     *
     * @return string
     */
    protected function getLogFile() : string
    {
        $filename = 'test';

        return "{$this->logFileDir}/{$filename}.log";
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
                    return true;
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
