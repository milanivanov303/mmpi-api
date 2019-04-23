<?php

use App\Models\User;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChainType;
use Modules\Instances\Models\InstanceType;

class InstancesTest extends RestTestCase
{
    protected $uri        = 'v1/instances';
    protected $table      = 'instances';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $owner           = EnumValue::where('type', 'instances_owner')->inRandomOrder()->first();
        $status          = EnumValue::where('type', 'active_inactive')->inRandomOrder()->first();
        $environmentType = DeliveryChainType::inRandomOrder()->first();
        $instanceType    = InstanceType::inRandomOrder()->first();

        return [
            'name'                      => 'CVS',
            'play_as_demo'              => 'n',
            'owner'                     => $owner->toArray(),
            'status'                    => $status->toArray(),
            'timezone'                  => 'Europe/Sofia',
            'host'                      => $faker->word(),
            'user'                      => $faker->username(),
            'db_user'                   => $faker->username(),
            'tns_name'                  => '',
            'has_patch_install_in_init' => 0,
            'instance_type'             => $instanceType->toArray(),
            'environment_type'          => $environmentType->toArray()
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
        $data['timezone'] = $faker->randomNumber();

        // remove required parameters
        unset($data['name'], $data['owner']);

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
        $data['timezone'] = 'Europe/Sofia';

        return $data;
    }
}
