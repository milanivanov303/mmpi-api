<?php

use App\Models\User;

class EnumValuesTest extends RestTestCase
{
    protected $uri        = 'v1/enum-values';
    protected $table      = 'enum_values';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        return [
            'type'        => $this->faker()->realText(10),
            'key'         => $this->faker()->realText(10),
            'value'       => $this->faker()->realText(10),
            'description' => $this->faker()->realText(15),
            'sortindex'   => $this->faker()->numberBetween(900, 950),
            'active'      => 1,       
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
        // Set invalid parameters
        $data['key'] = $this->faker()->randomNumber();

        // remove required parameters
        unset($data['type']);

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
        // Change parameters
        $data['value'] = $this->faker()->realText(10);

        //Remove date as it is overwritten on each request
        unset($data['changed_on']);
        
        return $data;
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = App\Models\EnumValue::inRandomOrder()->first();

        $this
            ->get( $this->uri . '/' .  $data->type . '/' . $data->key)
            ->seeJson($data->toArray())
            ->assertResponseOk();
    }
}
