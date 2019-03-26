<?php

namespace Modules\JsonRpc\Procedures;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Projects\Models\Project;
use Illuminate\Support\Facades\Auth;

class Cppcheck
{
    /**
     * Temporary directory name
     *
     * @var string
     */
    protected $tempDirectoryName;

    /**
     * CVS checkout command
     *
     * @var string
     */
    protected $cvsCheckoutCmd = "cvs -s CVSRMT=YES -d :pserver:%s:@cvs.codixfr.private:/app/cvs/repo checkout";

    /**
     * cppcheck command
     *
     * @var string
     */
    protected $cppCheckCmd = ". /enterprise/.custom_profile && cppcheck -q -DSYSTEME_64 -DSYSTEME_LINUX -I client-h/client.h";

    /**
     * Cppcheck constructor
     */
    public function __construct()
    {
        // add logged in user in cvs command
        $this->cvsCheckoutCmd = sprintf($this->cvsCheckoutCmd, 'iivanova'/*Auth::user()->getUsername()*/);
    }

    /**
     * Process procedure
     *
     * @param array $project
     * @param array $source
     * @return mixed
     *
     * @throws \Exception
     */
    public function run(array $project, array $source)
    {
        $project = Project::find(
            app(Project::class)->getModelId($project, 'name')
        );

        if (is_null($project)) {
            throw new \Exception('Project could not be found!');
        }

        $this->createTempDirectory();

        try {
            // checkout source
            $this->checkoutSource($source);

            // checkout client.h
            $this->checkoutClientH($project->clnt_cvs_dir);

            // run cppcheck
            $this->runCppcheck($source['name']);

            $this->deleteTempDirectory();
        } catch (\Exception $e) {
            $this->deleteTempDirectory();
            throw $e;
        }

        return 'success';
    }

    /**
     * Create temporary directory
     *
     * @throws \Exception
     */
    protected function createTempDirectory()
    {
        $this->tempDirectoryName = time() . '-' . random_int(1, 100);
        if (Storage::makeDirectory($this->tempDirectoryName) === false) {
            Log::error('Could not create temporary directory');
            throw new \Exception('Could not create checkout directory', 1);
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
        Storage::deleteDirectory($this->tempDirectoryName);
        Log::info('Temporary directory deleted successfully');
    }

    /**
     * Checkout source
     *
     * @param array $source
     * @throws \Exception
     */
    protected function checkoutSource(array $source)
    {
        $result = $this->exec("{$this->cvsCheckoutCmd}  -r {$source['version']} \"{$source['name']}\"");

        if ($result['exit_code'] || preg_match('/checkout: warning: new-born/', $result['output'])) {
            Log::error("Could not checkout source \"{$source['name']} - {$source['version']}\"");
            throw new \Exception($result['output'], 2);
        }

        Log::info("Source \"{$source['name']} - {$source['version']}\" checkout successfully");
    }

    /**
     * Checkout client.h
     *
     * @param string $clntCvsDir
     * @throws \Exception
     */
    protected function checkoutClientH(string $clntCvsDir)
    {
        $result = $this->exec("{$this->cvsCheckoutCmd}  -d client-h \"imxclt/{$clntCvsDir}/include/client.h\"");

        if ($result['exit_code']) {
            Log::error("Could not checkout client.h \"{$clntCvsDir}\"");
            throw new \Exception($result['output'], 3);
        }

        Log::info("client.h \"{$clntCvsDir}\" checkout successfully");
    }

    /**
     * Run cppcheck
     *
     * @param string $sourceName
     * @throws \Exception
     */
    protected function runCppcheck(string $sourceName)
    {
        $result = $this->exec(". /enterprise/.custom_profile && cppcheck -q -DSYSTEME_64 -DSYSTEME_LINUX -Iclient-h/client.h {$sourceName}");

        if ($result['exit_code'] || preg_match('/(error)/', $result['output'])) {
            throw new \Exception($result['output'], 4);
        }
    }

    /**
     * Exec shell command
     *
     * @param $cmd
     * @return array
     */
    protected function exec($cmd)
    {
        $directory = storage_path('app/' . $this->tempDirectoryName);
        $cmd       = "cd {$directory} && {$cmd} 2>&1";

        Log::info("Run \"{$cmd}\"");

        exec($cmd, $output, $exit_code);

        return [
            'exit_code' => $exit_code,
            'output'    => implode(PHP_EOL, $output)
        ];
    }
}
