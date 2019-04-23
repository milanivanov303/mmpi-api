<?php

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

        //Remove date as it is overwritten on each request
        unset($data['changed_on']);
        $data['value'] = $faker->text(5);

        return $data;
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = App\Models\EnumValue::where('key', 'idwg')->inRandomOrder()->first();
        $type = 'project_event_type';
        $key  = 'idwg';

        $this
            ->get( $this->uri . '/' .  $type . '/' . $key)
            ->seeJson($data->toArray())
            ->assertResponseOk();
    }
}
