<?php

use App\Helpers\DataMapper;

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
        $data = $this->mapper->mapRequestData([
            'owner'   => 'yarnaudov',
            'manager' => 'ivasilev'
        ]);

        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('manager_id', $data);

        $this->assertEquals('yarnaudov', $data['user_id']);
        $this->assertEquals('ivasilev', $data['manager_id']);
    }

    public function test_map_response_data()
    {
        $data = $this->mapper->mapResponseData([
            'user_id'    => 'yarnaudov',
            'manager_id' => 'ivasilev'
        ]);

        $this->assertArrayHasKey('owner', $data);
        $this->assertArrayHasKey('manager', $data);

        $this->assertEquals('yarnaudov', $data['owner']);
        $this->assertEquals('ivasilev', $data['manager']);
    }
}