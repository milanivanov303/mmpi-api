<?php

namespace Modules\Ddl\Services;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DdlService
{
    /**
     * Imx fe branch name
     *
     * @var string
     */
    protected $branch;

    /**
     * File content
     *
     * @var string
     */
    protected $content;

    /**
     * Commit message
     *
     * @var string
     */
    protected $commitMsg;

    /**
     * Temp directory
     *
     * @var string
     */
    protected $workDir;

    /**
     * Repostiroty name
     *
     * @var string
     */
    protected $repo;

    /**
     * File path and name
     *
     * @var string
     */
    protected $filePathAndName;

    /**
     * BuildService constructor.
     *
     * @param string $content
     * @param string $commitMsg
     * @param string $branch
     */
    public function __construct(
        string $content,
        string $commitMsg,
        string $branch
    ) {
        $this->content = $content;
        $this->commitMsg = $commitMsg;
        $this->branch = $branch;
        $this->workDir = storage_path('app');
        $this->repo = 'TEST_DDL';
        $this->filePathAndName = $this->workDir . '/' . $this->repo . '/' . $commitMsg . '.ddl';
    }

    /**
     * Run build
     *
     * @throws \Exception
     */
    public function run()
    {
        try {
            $this->cloneRepo();

            $this->createFile();

            $this->addFile();

            $this->commitFile();

            $this->push();

            $this->deleteTempDirectory();
        } catch (\Exception $e) {
            $this->deleteTempDirectory();
            throw $e;
        }

        return 'success';
    }

    /**
     * Delete temporary directory
     *
     * @throws \Exception
     */
    protected function deleteTempDirectory()
    {
        if (Storage::deleteDirectory($this->repo) === false) {
            Log::error('Could not delete temporary directory');
            throw new \Exception('Could not delete checkout directory', 3);
        }

        Log::info('Temporary directory deleted successfully');
    }

    /**
     * Clone repository
     *
     * @throws \Exception
     */
    protected function cloneRepo()
    {
        $clone = new Process([
            'hg',
            'clone',
            "http://rhode.codixfr.private/DevOps/iMXRefresh/$this->repo",
            '-r',
            $this->branch
        ], $this->workDir);
        
        $clone->setTimeout(600); // 10 min

        $clone->run();

        if (!$clone->isSuccessful()) {
            Log::error('Could not clone - ' . $clone->getErrorOutput());
            throw new \Exception('Could not clone - ' . $clone->getErrorOutput(), 3);
        }

        Log::info('Branch was cloned successfully');
    }

    /**
     * Create file
     *
     * @throws \Exception
     */
    protected function createFile()
    {
        $cmd = 'echo \'' . $this->content . '\' > ' . $this->filePathAndName;
        exec($cmd, $output, $exit_code);

        if ($exit_code || preg_match('/checkout: warning: new-born/', implode(PHP_EOL, $output))) {
            Log::error("Could not create file " . implode(PHP_EOL, $output));
            throw new \Exception("Could not create file ".  implode(PHP_EOL, $output), 4);
        }

        Log::info("File was created successfully");
    }

    /**
     * Add file
     *
     * @throws \Exception
     */
    protected function addFile()
    {
        $add = new Process(['hg', 'add', $this->filePathAndName]);
        $add->run();

        if (!$add->isSuccessful()) {
            Log::error('Could not add file - ' . $add->getErrorOutput());
            throw new \Exception('Could not add file - ' . $add->getErrorOutput(), 3);
        }

        Log::info('File was added successfully');
    }

    /**
     * Commit file
     *
     * @throws \Exception
     */
    protected function commitFile()
    {
        $commit = new Process([
            'hg',
            'commit',
            '-u',
            Auth::user()->username . '@codixfr.private',
            '-m',
            $this->commitMsg
        ], $this->workDir . '/' . $this->repo);

        $commit->run();

        if (!$commit->isSuccessful()) {
            Log::error('Could not commit - ' . $commit->getErrorOutput());
            throw new \Exception('Could not commit - ' . $commit->getErrorOutput(), 3);
        }

        Log::info('File was commited successfully');
    }

    /**
     * Push
     *
     * @throws \Exception
     */
    protected function push()
    {
        $push = new Process([
            'hg',
            'push',
            "http://rhode.codixfr.private/DevOps/iMXRefresh/$this->repo",
            '-b',
            $this->branch
        ], $this->workDir . '/' . $this->repo);

        $push->run();

        if (!$push->isSuccessful()) {
            Log::error('Could not push - ' . $push->getErrorOutput());
            throw new \Exception('Could not push - ' . $push->getErrorOutput(), 3);
        }

        Log::info('Successfully push changes');
    }
}
