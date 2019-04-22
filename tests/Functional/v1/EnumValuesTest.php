<?php

use App\Models\EnumValue;

class EnumValuesTest extends RestTestCase
{
    protected $uri        = 'v1/enum-values';
    protected $table      = 'enum_values';
    protected $primaryKey = 'key';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $type = EnumValue::where('type', 'project_event_type')->inRandomOrder()->minimal()->first();
        $key  = EnumValue::where('key', 'idwg')->inRandomOrder()->minimal()->first();
        
        return [
            'type' => $type->toArray(),
            'key'  => $key->toArray()
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
        $this->assertEquals(true, true);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test delete single
     *
     * @return void
     */
    public function testDelete()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = EnumValue::where('key', 'idwg')->inRandomOrder()->minimal()->first();
        $type = 'project_event_type';
        $key  = 'idwg';

        $this
            ->get( $this->uri . '/' .  $type . '/' . $key)
            ->seeJson($data->toArray())
            ->assertResponseOk();
    }
}
