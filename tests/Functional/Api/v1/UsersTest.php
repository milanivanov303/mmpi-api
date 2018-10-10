<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UsersTest extends TestCase
{
    use DatabaseTransactions;
    
    public function setUp() {
        parent::setUp();
        $this->actingAs(User::first());
    }

    /**
     * Test get single user
     * 
     * @return void
     */
    public function testGetUser()
    {
        $user = User::first();

        $this
            ->get('/api/v1/users/' . $user->username)
            ->shouldReturnJson()
            ->seeJson($user->toArray())
            ->assertResponseOk();
    }

    /**
     * Test get non existing user
     *
     * @return void
     */
    public function testGetNonExistingUser()
    {
        $this
            ->get('/api/v1/users/NON-EXISTING-HASH')
            ->assertResponseStatus(404);
    }
    
    /**
     * Test get users list
     * 
     * @return void
     */
    public function testGetUsersList()
    {
        $this
            ->json('GET', '/api/v1/users?limit=10')
            ->shouldReturnJson()
            ->seeJsonStructure(['data'])
            ->assertResponseOk();
    }


    /**
     * Test get paginated users list
     *
     * @return void
     */
    public function testGetPaginatedUsersList()
    {
        $this
            ->json('GET', '/api/v1/users?page=3')
            ->shouldReturnJson()
            ->seeJsonStructure(['meta' => ['pagination' => ['total', 'current_page']], 'data'])
            ->assertResponseOk();
    }
}
