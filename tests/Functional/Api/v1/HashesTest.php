<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HashesTest extends TestCase
{
    use DatabaseTransactions;
    
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
            'owner'        => 'astamenov',
            'repo_path'    => '/extranet/hg/v9_be',
            'repo_url'     => 'http://lemon.codixfr.private:6002/v9_be',
            'rev'          => bin2hex(random_bytes(10))
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
            ->json('POST', '/api/v1/hashes', $data)
            ->seeJson($data)
            ->assertResponseStatus(201);
        
        $this->seeInDatabase('hash_commits', ['hash_rev' => $data['rev']]);
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
            ->json('POST', '/api/v1/hashes', $data)
            ->seeJsonStructure(['branch', 'owner', 'rev'])
            ->assertResponseStatus(422);

        //var_dump($this->response->status(), $this->response->getData());

        $this->missingFromDatabase('hash_commits', ['hash_rev' => $data['rev']]);
    }
    
    /**
     * Test get single hash
     * 
     * @return void
     */
    public function testGetHash()
    {
        $data = $this->getData();

        $this->json('POST', '/api/v1/hashes', $data);

        $this
            ->get('/api/v1/hashes/' . $data['rev'])
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
            ->get('/api/v1/hashes/NON-EXISTING-HASH')
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
        
        $this->json('POST', '/api/v1/hashes', $data);

        // Change parameters
        $data['description'] = 'UPDATED_DESCRIPTION';

        $this
            ->json('PUT', '/api/v1/hashes/' . $data['rev'], $data)
            ->seeJson($data)
            ->assertResponseOk();
        
        $this->seeInDatabase('hash_commits', ['hash_rev' => $data['rev']]);
    }

    /**
     * Test get single hash
     *
     * @return void
     */
    public function testDeleteHash()
    {
        $data = $this->getData();

        $this->json('POST', '/api/v1/hashes', $data);

        $this
            ->json('DELETE', '/api/v1/hashes/' . $data['rev'])
            ->assertResponseStatus(204);

        $this->missingFromDatabase('hash_commits', ['hash_rev' => $data['rev']]);
    }
    
    /**
     * Test get hash list
     * 
     * @return void
     */
    public function testGetHashesList()
    {
        $this
            ->json('GET', '/api/v1/hashes')
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
            ->json('GET', '/api/v1/hashes?page=3')
            ->seeJsonStructure(['meta' => ['pagination' => ['total', 'current_page']], 'data'])
            ->assertResponseOk();
    }
}
