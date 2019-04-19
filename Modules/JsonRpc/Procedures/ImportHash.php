<?php

namespace Modules\JsonRpc\Procedures;

use App\Models\EnumValue;
use Core\Helpers\SSH2;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class ImportHash
{
    /**
     * Process procedure
     *
     * @param string $rev
     * @param string $module
     *
     * @throws \Exception
     */
    public function import(string $rev, string $module)
    {
        $repository = EnumValue::where('type', 'repository_type')->where('key', $module)->first();

        if ($repository) {
            if ($repository->subtype === 'repo_hg') {
                $hash = $this->hg($rev, $repository);
            }

            if ($repository->subtype === 'repo_git') {
                // get git rev info
            }

            // import hash
            if ($hash) {
                // Make use of hashes routes
                $response = app()->handle(
                    app('request')->create('v1/hashes', 'POST', $hash)
                );

                if ($response->isSuccessful()) {
                    return $response;
                }
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

        // login to server
        if (!$ssh2->login('yarnaudov', 'ND2700k$1')) {
            throw new \Exception("Could login to {$repository->url}");
        }

        // set working directory to use for all commands
        $ssh2->setWorkDir($repository->extra_property);

        // Get revision data
        $hash = $ssh2->exec('hg log --rev ' . $rev . ' --template \'
                \{
                    "branch": "{ifeq(branch, "default", "default", "{branch}")}",
                    "merge_branch": "{revset("parents(%d)", rev) % "{ifeq(branch, "default", "default", "{branch}")}"}",
                    "hash_rev": "{node}",
                    "rev": "{rev}",
                    "version": "{tags}",
                    "description": {desc | json},
                    "parents": "{parents}",
                    "commited_by": {"username": "{author|user}"},
                    "timestamp": "{date}",
                }
                \'');

        $hash = json_decode($hash, JSON_FORCE_OBJECT);
        if (json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }

        // Get parent hash. p1 looks like is the parent from the same branch
        $parentRev = $ssh2->exec("hg log --rev {$rev} --template '{p1node}'");

        // Get files changed from p1:hash
        $files = $ssh2->exec("hg status --rev {$parentRev}:{$rev} -n");

        $hash['files'] = array_filter(
            explode(PHP_EOL, $files)
        );

        return $hash;
    }
}
