<?php

use \Modules\DistributionGroups\Models\DistributionGroup;

class DistributionGroupTest extends RestTestCase
{
    protected $uri        = 'v1/distribution_groups';
    protected $table      = 'distribution_groups';
    protected $primaryKey = 'distribution_groups_id';

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
     * Test create
     */
    public function testCreate()
    {
        $this
            ->json('POST', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {

        return [
            'samaccountname'     => $this->faker()->text(10),
            'displayname'        => $this->faker()->text(15),
            'distinguished_name' => $this->faker()->text(15),
            'created_date'       => \Carbon\Carbon::now(),
            'email'              => $this->faker()->email,
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
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = DistributionGroup::first()->toArray();

        $this
            ->get( $this->uri . '/' . $data['samaccountname'])
            ->seeJson($data)
            ->assertResponseOk();
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
}
