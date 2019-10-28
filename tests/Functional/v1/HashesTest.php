<?php

use App\Models\EnumValue;
use App\Models\User;
use Modules\Hashes\Models\HashBranch;

class HashesTest extends RestTestCase
{
    protected $uri        = 'v1/hashes';
    protected $table      = 'hash_commits';
    protected $primaryKey = 'hash_rev';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        try {
            $hashRev = bin2hex(random_bytes(10));
            $rev     = random_int(1, 1000);
        } catch (\Exception $exception) {}

        $user     = User::inRandomOrder()->active()->first();
        $repoType = EnumValue::where('type', 'repository_type')->inRandomOrder()->first();
        $branch   = HashBranch::where('repo_type_id', $repoType->id)->inRandomOrder()->first();

        return [
            'branch'       => $branch->toArray(),
            'description'  => '
                TTS KEY*:      IXDEV-7266
                               IXDEV-7290
                FUNC CHANGES*: Add fix of generation of suffix for adresse and tty
                TECH CHANGES*: Add check on sessions with same adresse
                MERGE:         c00be5a14a4c6b69af8f45b7e3390187c7da822a
                DEPENDENCIES:  G_BU, G_INDIVIDU, EXT_SYS_INTERVENANTS
            ',
            'files'        => [
                ['name' => 'etc/configs/MOLOTCWALLET/imx_backend.properties'],
                ['name' => 'etc/configs/MOLOTCWALLET/imx_backend.xml'],
                ['name' => 'etc/configs/MOLOTCWALLET/wallet/cwallet.sso'],
                ['name' => 'etc/configs/MOLOTCWALLET/wallet/tnsnames.ora'],
                ['name' => 'pom.xml']
             ],
            'merge_branch' => '_DEV_IXDEV-1763 e_honor_param backend',
            'repo_type'    => $repoType->toArray(),
            'committed_by' => $user->toArray(),
            'hash_rev'     => $hashRev,
            'rev'          => $rev,
            'version'      => 'v1.2.3'
        ];
    }

    /**
     * Get request invalid data
     *
     * @param array $data
     * @return array
     */
    protected function getInvalidData(array $data)
    {
        // Set invalid parameters
        $data['committed_by'] = 'INVALID_OWNER';
        $data['rev']          = 'INVALID_REV';

        // remove required parameters
        unset($data['repo_type']);

        return $data;
    }

    /**
     * Get request update data
     *
     * @param array $data
     * @return array
     */
    protected function getUpdateData(array $data)
    {
        //Remove date as it is overwritten on each request
        unset($data['made_on']);

        // Change parameters
        $data['description'] = 'UPDATED_DESCRIPTION';

        return $data;
    }
}
