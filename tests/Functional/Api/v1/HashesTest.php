<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HashesTest extends TestCase
{
    use DatabaseTransactions;

    protected $uri        = 'api/v1/hashes';
    protected $table      = 'hash_commits';
    protected $primaryKey = 'hash_rev';

    public function setUp() {
        parent::setUp();
        $this->actingAs(User::first());
    }
    /**
     * Get request data
     *
     * @return array
     */
    public function getData()
    {
        try {
            $rev = bin2hex(random_bytes(10));
        } catch (\Exception $exception) {}

        $user = User::inRandomOrder()->first();

        return [
            'branch'       => 'default',
            'chains'       => [
                'bcol_imx_v9_rel',
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
     * Test creation of hash
     *
     * @return void
     */
    public function testCreateHash()
    {
        $data = $this->getData();

        $this
            ->json('POST', $this->uri, $data)
            ->seeJson($data)
            ->assertResponseStatus(201);

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $data['rev']
        ]);

    }

    /**
     * Test creation of hash with wrong data
     *
     * @return void
     */
    public function testCreateHashWithInvalidData()
    {
        $data = $this->getData();

        // Set invalid parameters
        $data['owner'] = 'INVALID_OWNER';
        $data['rev']   = 'INVALID_REV';

        // remove required parameters
        unset($data['branch']);

        $this
            ->json('POST', $this->uri, $data)
            ->seeJsonStructure(['branch', 'owner', 'rev'])
            ->assertResponseStatus(422);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data['rev']
        ]);
    }

    /**
     * Test get single hash
     *
     * @return void
     */
    public function testGetHash()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->get($this->uri . '/' . $data['rev'])
            ->seeJson($data)
            ->assertResponseOk();
    }

    /**
     * Test get non existing hash
     *
     * @return void
     */
    public function testGetNonExistingHash()
    {
        $this
            ->get($this->uri . '/NON-EXISTING-HASH')
            ->assertResponseStatus(404);
    }

    /**
     * Test update of hash
     *
     * @return void
     */
    public function testUpdateHash()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        // Change parameters
        $data['description'] = 'UPDATED_DESCRIPTION';

        $this
            ->json('PUT', $this->uri . '/' . $data['rev'], $data)
            ->seeJson($data)
            ->assertResponseOk();

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $data['rev']
        ]);
    }

    /**
     * Test get single hash
     *
     * @return void
     */
    public function testDeleteHash()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->json('DELETE', $this->uri . '/' . $data['rev'])
            ->assertResponseStatus(204);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data['rev']
        ]);
    }

    /**
     * Test get hash list
     *
     * @return void
     */
    public function testGetHashesList()
    {
        $this
            ->json('GET', $this->uri . '?limit=100')
            ->shouldReturnJson()
            ->seeJsonStructure(['data'])
            ->assertResponseOk();
    }


    /**
     * Test get paginated hash list
     *
     * @return void
     */
    public function testGetPaginatedHashesList()
    {
        $this
            ->json('GET', $this->uri . '?page=3')
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
