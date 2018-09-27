<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HashesTest extends TestCase
{
    //use DatabaseTransactions;
    
    public function setUp() {
        parent::setUp();
        $this->actingAs(User::first());
    }
    
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
            'rev'          => 'sfdvbe5675uhrtn678'
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
     * Test get single hash
     * 
     * @return void
     */
    public function testGetHash()
    {
        $data = $this->getData();
        
        $this
            ->json('GET', '/api/v1/hashes/' . $data['rev'])
            ->seeJson($data)
            ->assertResponseOk();
    }
    
    /**
     * Test update of hash
     * 
     * @return void
     */
    public function testUpdateHash()
    {
        $data = $this->getData();
        $data['description'] = 'Updated description';

        $this
            ->json('PUT', '/api/v1/hashes/' . $data['rev'], $data)
            ->seeJson($data)
            ->assertResponseOk();
        
        $this->seeInDatabase('hash_commits', ['hash_rev' => $data['rev']]);
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
            ->assertResponseOk();
    }
}
