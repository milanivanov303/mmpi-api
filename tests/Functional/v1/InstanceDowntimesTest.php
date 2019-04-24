<?php

class InstanceSowntimesTest extends RestTestCase
{
    protected $uri        = 'v1/instance-downtimes';
    protected $table      = 'instance_downtimes';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $instance = \Modules\Instances\Models\Instance::inRandomOrder()->first();

        return [
            'instance'           => $instance->toArray(),
            'start_datetime'     => $faker->date('Y-m-d H:i:s'),
            'end_datetime'       => $faker->date('Y-m-d H:i:s'),
            'status'             => (int)$faker->boolean(),
            'description'        => $faker->text(60)
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

        // remove required parameters
        unset($data['instance']);

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
        unset($data['made_on']);
        $data['start_datetime'] = $faker->date('Y-m-d H:i:s');

        return $data;
    }
}
