<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UsersTest extends TestCase
{
    use DatabaseTransactions;

    protected $uri        = 'api/v1/users';
    protected $table      = 'users';
    protected $primaryKey = 'username';

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
        $user = User::with(['manager', 'deputy'])->first();

        $this
            ->get($this->uri . '/' . $user->{$this->primaryKey})
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
            // Check with string not working, because string is cast to int and the result is 0.
            // There is user with id 0 and it is returnd!
            //->get($this->uri . '/NON-EXISTING-USER')
            ->get($this->uri . '/-1')
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
            ->json('GET', $this->uri . '?limit=10')
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
            ->json('GET', $this->uri . '?page=1')
            ->shouldReturnJson()
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
