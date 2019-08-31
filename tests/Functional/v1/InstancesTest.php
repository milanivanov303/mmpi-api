<?php

use App\Models\User;
use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChainType;
use Modules\Instances\Models\InstanceType;
use Modules\DeliveryChains\Models\DeliveryChain;

class InstancesTest extends RestTestCase
{
    protected $uri        = 'v1/instances';
    protected $table      = 'instances';
    protected $primaryKey = 'id';

    protected $with = [
        'owner',
        'status',
        'environment_type',
        'instance_type',
        'delivery_chains'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $owner           = EnumValue::where('type', 'instances_owner')->inRandomOrder()->first();
        $status          = EnumValue::where('type', 'active_inactive')->inRandomOrder()->first();
        $environmentType = DeliveryChainType::inRandomOrder()->first();
        $instanceType    = InstanceType::inRandomOrder()->first();
        $deliveryChains  = DeliveryChain::active()->inRandomOrder()->limit(3)->get();

        return [
            'name'                      => 'CVS',
            'play_as_demo'              => 'n',
            'owner'                     => $owner->toArray(),
            'status'                    => $status->toArray(),
            'timezone'                  => 'Europe/Sofia',
            'host'                      => $this->faker()->word(),
            'user'                      => $this->faker()->username(),
            'db_user'                   => $this->faker()->username(),
            'tns_name'                  => '',
            'has_patch_install_in_init' => 0,
            'instance_type'             => $instanceType->toArray(),
            'environment_type'          => $environmentType->toArray(),
            'delivery_chains'           => $deliveryChains->toArray()
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
        $data['timezone'] = $this->faker()->randomNumber();

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
