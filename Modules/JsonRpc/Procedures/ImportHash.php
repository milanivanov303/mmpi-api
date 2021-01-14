<?php

namespace Modules\JsonRpc\Procedures;

use App\Models\EnumValue;

class ImportHash
{
    /**
     * Path to import hg hash php script
     *
     * @var string
     */
    protected $importScriptPath = '/home/rhode/devops/hooks/scripts/import_hg_hash.php';

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
                return $this->hgImport($hash, $repository);
            }
        }
    }

    /**
     * hgImport data
     *
     * @param string $rev
     * @param EnumValue $repository
     * @return array
     * @throws \Exception
     */
    protected function hgImport(string $rev, EnumValue $repository)
    {
        $ssh2 = app('SSH', ['host' => 'rhode']);
        $ssh2->setWorkDir("/home/rhode/repos/{$repository->extra_property}/.hg");

        $importHash = $ssh2->execAs(
            "rhode",
            "php {$this->importScriptPath} {$rev} {$repository->key} sync"
        );

        if ($ssh2->getExitStatus() !== 0) {
            throw new \Exception($importHash, $ssh2->getExitStatus());
        }

        if ($importHash) {
            $importHash = preg_split('/\r\n|\r|\n/', trim($importHash));
        }

        return $importHash;
    }
}
