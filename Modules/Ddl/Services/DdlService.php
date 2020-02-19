<?php

namespace Modules\Ddl\Services;

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
     * File name
     *
     * @var string
     */
    protected $fileName;

    /**
     * Temp directory
     *
     * @var string
     */
    protected $workDir;

    /**
     * BuildService constructor.
     *
     * @param string $content
     * @param string $fileName
     * @param string $branch
     */
    public function __construct(
        string $content,
        string $fileName,
        string $branch
    ) {
        $this->content = $content;
        $this->fileName = $fileName . '.ddl';
        $this->branch = $branch;
        $this->workDir = $branch;
    }

    /**
     * Run build
     *
     * @throws \Exception
     */
    public function run()
    {
        try {
            $this->createWorkDirIfNotExists();

        // $this->cloneRepo();

        // $this->createFile();

        // $this->addFile();

        // $this->commitFile();

        // $this->push();

        // $this->deleteTempDirectory();
        } catch (\Exception $e) {
            $this->deleteTempDirectory();
            throw $e;
        }

        return 'success';
    }

    /**
     * Create work directory if not exists
     */
    protected function createWorkDirIfNotExists()
    {
        if (Storage::makeDirectory($this->workDir) === false) {
            Log::error('Could not create temporary directory');
            throw new \Exception('Could not create temporary directory', 3);
        }

        Log::info('Temporary directory created successfully');
    }

    /**
     * Delete temporary directory
     *
     * @throws \Exception
     */
    protected function deleteTempDirectory()
    {
        if (Storage::deleteDirectory($this->workDir) === false) {
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
        // must update url
        $clone = new Process(['hg', 'clone', "http://rhode.codixfr.private/$this->branch", $this->workDir]);
        $clone->setTimeout(600); // 10 min

        $clone->run();

        if (!$clone->isSuccessful()) {
            Log::error('Could not clone branch');
            throw new \Exception('Could not clone branch', 3);
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
        Storage::put($this->workDir . '/' . $this->fileName, $this->content);

        if (!Storage::exists($this->workDir . '/' . $this->fileName . '.ddl')) {
            Log::error('Could not create file');
            throw new \Exception('Could not create file', 3);
        }

        Log::info('File was created successfully');
    }

    /**
     * Add file
     *
     * @throws \Exception
     */
    protected function addFile()
    {
        $add = new Process(['hg', 'add'], $this->fileName);
        $add->run();

        if (!$add->isSuccessful()) {
            Log::error('Could not add file');
            throw new \Exception('Could not add file', 3);
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
        $commit = new Process(['hg', 'commit', '-m', "Added $this->fileName"]);
        $commit->run();

        if (!$commit->isSuccessful()) {
            Log::error('Could not commit file');
            throw new \Exception('Could not commit file', 3);
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
        $push = new Process(['hg', 'push']);
        $push->run();

        if (!$push->isSuccessful()) {
            Log::error('Could not push');
            throw new \Exception('Could not push', 3);
        }

        Log::info('Successfully push changes');
    }
}
