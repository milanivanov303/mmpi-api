<?php

namespace Modules\JsonRpc\Procedures;

use App\Models\EnumValue;
use Carbon\Carbon;
use Core\Helpers\SSH2;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Hashes\Jobs\GenerateRamlDocumentation;
use Modules\Hashes\Jobs\HgBuild;

class ImportHash
{
    /**
     * Process procedure
     *
     * @param string $hash
     * @param string $repoType
     *
     * @throws \Exception
     */
    public function import(string $hash, string $repo_type)
    {
        $repository = EnumValue::where('type', 'repository_type')->where('key', $repo_type)->first();

        if ($repository) {
            if ($repository->subtype === 'repo_hg') {
                $data = $this->hg($hash, $repository);
            }

            if ($repository->subtype === 'repo_git') {
                // get git rev info
            }

            // import hash
            if ($data) {
                $data['repo_type'] = ["key" => $repository->key];

                // search for changed raml files and execute generate documentation job
                $ramlFiles = collect($data['files'])->filter(function ($file) {
                    return false !== strpos(strtolower($file['name']), '.raml');
                });
                if ($ramlFiles->count()) {
                    dispatch(
                        (new GenerateRamlDocumentation($data['hash_rev'], $ramlFiles))
                            ->onQueue('raml')
                    );
                }

                // Make use of hashes routes
                $response = app()->handle(
                    app('request')->create('v1/hashes', 'POST', $data)
                );

                if ($response->isSuccessful()) {
                    // execute hg build job
                    dispatch(
                        (new HgBuild($data['hash_rev'], $data['branch']))->onQueue('build')
                    );

                    return $response->getData();
                }

                throw new \Exception($response->getContent(), $response->getStatusCode());
            }
        }
    }

    /**
     * Get hg data
     *
     * @param string $rev
     * @param EnumValue $repository
     * @return array
     * @throws \Exception
     */
    protected function hg(string $rev, EnumValue $repository)
    {
        $ssh2 = new SSH2(parse_url($repository->url, PHP_URL_HOST));

        $username = config('app.repository.username');

        try {
            $key = Storage::get(config('app.repository.public_key'));
        } catch (FileNotFoundException $e) {
            Log::error("Repository public key file not found");
            return null;
        }

        // login to server
        if (!$ssh2->loginRSA($username, $key)) {
            throw new \Exception("Could login to {$repository->url}");
        }

        // set working directory to use for all commands
        $ssh2->setWorkDir($repository->extra_property);

        // Get revision data
        $hash = $ssh2->exec('hg log --rev ' . $rev . ' --template \'
                \{
                    "branch": "{ifeq(branch, "default", "default", "{branch}")}",
                    "merge_branch": "{revset("parents(%d)", rev) % "{ifeq(branch, "default", "", "{branch}")}"}",
                    "hash_rev": "{node}",
                    "rev": "{rev}",
                    "version": "{tags}",
                    "description": {desc|json},
                    "committed_by": "{author|user}",
                    "repo_timestamp": "{date}"
                }
                \'');

        if ($ssh2->getExitStatus() !== 0) {
            throw new \Exception($hash, $ssh2->getExitStatus());
        }

        $hash = json_decode($hash, JSON_FORCE_OBJECT);
        if (json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }

        // Get parent hash. p1 looks like is the parent from the same branch
        $parentRev = $ssh2->exec("hg log --rev {$rev} --template '{p1node}'");

        // Get files changed from p1:hash
        $files = $ssh2->exec("hg status --rev {$parentRev}:{$rev} -n");
        $files = array_filter(explode(PHP_EOL, $files));

        $hash['files'] = array_map(function ($file) {
            return ["name" => $file];
        }, $files);

        $hash['rev']            = (int) $hash['rev'];
        $hash['branch']         = ["name" => $hash['branch']];
        $hash['committed_by']   = ["username" => $hash['committed_by']];
        $hash['repo_timestamp'] = Carbon::createFromTimestamp($hash['repo_timestamp'])->format('Y-m-d H:i:s');

        return $hash;
    }
}
