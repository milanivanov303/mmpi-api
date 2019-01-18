<?php

use App\Models\User;
use App\Models\Department;
use App\Models\AccessGroup;

class UsersTest extends RestTestCase
{
    protected $uri        = 'v1/users';
    protected $table      = 'users';
    protected $primaryKey = 'username';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $department  = Department::inRandomOrder()->first();
        $accessGroup = AccessGroup::inRandomOrder()->first();

        return [
            'name'         => $faker->name(),
            'username'     => $faker->username(),
            'email'        => $faker->email(),
            'sid'          => $faker->word(),
            'sidfr'        => $faker->word(),
            'uidnumber'    => null,
            'status'       => 0,
            'manager'      => null,
            'deputy'       => null,
            'department'   => $department->toArray(),
            'access_group' => $accessGroup->toArray()
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
        return $data;
    }

    /**
     * Test creation
     *
     * @return void
     */
    public function testCreate()
    {
        $this
            ->json('POST', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $this
            ->json('POST', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $data = $this->getData();

        $this
            ->json('PUT', $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->assertResponseStatus(405);
    }

    /**
     * Test delete single
     *
     * @return void
     */
    public function testDelete()
    {
        $data = $this->getData();

        $this
            ->json('DELETE', $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->assertResponseStatus(405);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = User::inRandomOrder()->first()->toArray();

        $this
            ->get( $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->seeJson($data)
            ->assertResponseOk();
    }
}
