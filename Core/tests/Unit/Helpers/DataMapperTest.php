<?php

use Core\Helpers\DataMapper;

class DataMapperTest extends TestCase
{
    protected $mapper;

    public function setUp()
    {
        parent::setUp();

        $this->mapper = new DataMapper([
            'user_id'    => 'owner',
            'manager_id' => 'manager'
        ]);
    }

    public function test_map_request_data()
    {
        $faker = Faker\Factory::create();

        $owner   = $faker->userName;
        $manager = $faker->userName;

        $data = $this->mapper->mapRequestData([
            'owner'   => $owner,
            'manager' => $manager
        ]);

        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('manager_id', $data);

        $this->assertEquals($owner, $data['user_id']);
        $this->assertEquals($manager, $data['manager_id']);
    }

    public function test_map_response_data()
    {
        $faker = Faker\Factory::create();

        $owner   = $faker->userName;
        $manager = $faker->userName;

        $data = $this->mapper->mapResponseData([
            'user_id'    => $owner,
            'manager_id' => $manager
        ]);

        $this->assertArrayHasKey('owner', $data);
        $this->assertArrayHasKey('manager', $data);

        $this->assertEquals($owner, $data['owner']);
        $this->assertEquals($manager, $data['manager']);
    }

    public function test_map_request_attribute()
    {
        $this->assertEquals('user_id', $this->mapper->mapRequestAttribute('owner'));
        $this->assertEquals('NOT-MAPPED-ATTRIBUTE', $this->mapper->mapRequestAttribute('NOT-MAPPED-ATTRIBUTE'));
    }

    public function test_map_response_attribute()
    {
        $this->assertEquals('owner', $this->mapper->mapResponseAttribute('user_id'));
        $this->assertEquals('NOT-MAPPED-ATTRIBUTE', $this->mapper->mapResponseAttribute('NOT-MAPPED-ATTRIBUTE'));
    }
}