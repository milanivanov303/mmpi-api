<?php

use PHPUnit\Framework\TestCase;

use App\Helpers\DataMapper;

class DataMapperTest extends TestCase
{
    protected $mapper;

    public function setUp()
    {
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
    }

    public function test_map_response_data()
    {
        $data = $this->mapper->mapResponseData([
            'user_id'    => 'yarnaudov',
            'manager_id' => 'ivasilev'
        ]);

        $this->assertArrayHasKey('owner', $data);
        $this->assertArrayHasKey('manager', $data);
    }
}