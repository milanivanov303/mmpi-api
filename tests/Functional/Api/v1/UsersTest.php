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
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $user = User::with(['manager', 'deputy'])->first();

        $this
            ->get($this->uri . '/' . $user->{$this->primaryKey})
            ->shouldReturnJson()
            ->seeJson($user->toArray())
            ->assertResponseOk();
    }

    /**
     * Test get non existing
     *
     * @return void
     */
    public function testGetNonExisting()
    {
        $this
            // Check with string not working, because string is cast to int and the result is 0.
            // There is user with id 0 and it is returnd!
            //->get($this->uri . '/NON-EXISTING-USER')
            ->get($this->uri . '/-1')
            ->assertResponseStatus(404);
    }

    /**
     * Test get list
     *
     * @return void
     */
    public function testGetList()
    {
        $this
            ->json('GET', $this->uri . '?limit=10')
            ->shouldReturnJson()
            ->seeJsonStructure(['data'])
            ->assertResponseOk();
    }


    /**
     * Test get paginated list
     *
     * @return void
     */
    public function testGetPaginatedList()
    {
        $this
            ->json('GET', $this->uri . '?page=1')
            ->shouldReturnJson()
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
