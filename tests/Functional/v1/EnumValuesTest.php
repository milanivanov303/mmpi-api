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
        $faker = Faker\Factory::create();

        return [
            'type'        => $faker->text(5),
            'key'         => $faker->text(5),
            'value'       => $faker->text(5),
            'description' => $faker->text(15),           
            'sortindex'   => $faker->numberBetween(900, 950),
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
        $faker = Faker\Factory::create();

        // Set invalid parameters
        $data['key'] = $faker->randomNumber();

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
        $faker = Faker\Factory::create();

        // Change parameters
        $data['value'] = $faker->text(5);

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
