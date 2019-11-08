<?php

use App\Models\EnumValue;
use App\Models\Department;

class DepartmentsTest extends RestTestCase
{
    protected $uri        = 'v1/departments';
    protected $table      = 'departments';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $departmentType = EnumValue::inRandomOrder()->first();

        return [
            'name'                    => $this->faker()->name(),
            'hr_department_id'        => $this->faker()->randomNumber(),
            'default_access_group_id' => $this->faker()->randomNumber(),
            'src_dlv_by_revision'     => $this->faker()->numberBetween(0, 1),
            'status'                  => $this->faker()->numberBetween(0, 1),
            'department_type'         => $departmentType->toArray()
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
        $data = Department::inRandomOrder()->first()->toArray();

        $this
            ->get( $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->seeJson($data)
            ->assertResponseOk();
    }
}
