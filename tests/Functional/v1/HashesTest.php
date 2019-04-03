<?php

use App\Models\User;

class HashesTest extends RestTestCase
{
    protected $uri              = 'v1/hashes';
    protected $table            = 'hash_commits';
    protected $primaryKey       = 'hash_rev';
    protected $primaryKeyMapped = 'rev';

    /**
     * Get primary key value
     *
     * @param array $data
     * @return mixed
     */
    protected function getPrimaryKeyValue($data)
    {
        return $data['rev'];
    }

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        try {
            $rev = bin2hex(random_bytes(10));
        } catch (\Exception $exception) {}

        $user = User::inRandomOrder()->active()->first();

        return [
            'branch'       => 'default',
            'chains'       => [
                'bcol_imx_v9_rel'
            ],
            'description'  => '
                TTS KEY*:      IXDEV-7266
                               IXDEV-7290
                FUNC CHANGES*: Add fix of generation of suffix for adresse and tty
                TECH CHANGES*: Add check on sessions with same adresse
                MERGE:         c00be5a14a4c6b69af8f45b7e3390187c7da822a
                DEPENDENCIES:  G_BU, G_INDIVIDU, EXT_SYS_INTERVENANTS
            ',
            'files'        => [
                'etc/configs/MOLOTCWALLET/imx_backend.properties',
                'etc/configs/MOLOTCWALLET/imx_backend.xml',
                'etc/configs/MOLOTCWALLET/wallet/cwallet.sso',
                'etc/configs/MOLOTCWALLET/wallet/tnsnames.ora',
                'pom.xml'
             ],
            'merge_branch' => '_DEV_IXDEV-1763 e_honor_param backend',
            'module'       => 'imx_be',
            'owner'        => $user->username,
            'repo_path'    => '/extranet/hg/v9_be',
            'repo_url'     => 'http://lemon.codixfr.private:6002/v9_be',
            'rev'          => $rev
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
        $data['owner'] = 'INVALID_OWNER';
        $data['rev']   = 'INVALID_REV';

        // remove required parameters
        unset($data['branch']);

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
        // Change parameters
        $data['description'] = 'UPDATED_DESCRIPTION';

        return $data;
    }

    /**
     * Test creation
     *
     * @return void
     */
    public function testCreate()
    {
        $data = $this->getData();
        $data = $this->create($data);

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $this->getPrimaryKeyValue($data)
        ]);

        // count saved chains in db
        $count = $this->app->make('db')->table('hash_commit_to_chains')->where([
            'hash_commit_id'  => $data['id'],
        ])->count();

        $this->assertEquals(1, $count);

        // count saved files in db
        $count = $this->app->make('db')->table('hash_commit_files')->where([
            'hash_commit_id'  => $data['id'],
        ])->count();

        $this->assertEquals(5, $count);
    }
}
