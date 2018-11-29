<?php

use App\Models\User;

class HashesTest extends RestTestCase
{
    protected $uri              = 'api/v1/hashes';
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

        $user = User::inRandomOrder()->first();

        return [
            'branch'       => 'default',
            'chains'       => [
                'bcol_imx_v9_rel'
            ],
            'description'  => 'IXDEV-1650 e_honor_param backend\n\nadd MOLO as mvn profile',
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
     * @return array
     */
    protected function getInvalidData($data)
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
     * @return array
     */
    protected function getUpdateData($data)
    {
        // Change parameters
        $data['description'] = 'UPDATED_DESCRIPTION';

        return $data;
    }
}
