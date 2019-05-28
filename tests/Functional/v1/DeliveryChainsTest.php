<?php

use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChainType;

class DeliveryChainsTest extends RestTestCase
{
    protected $uri        = 'v1/delivery-chains';
    protected $table      = 'delivery_chains';
    protected $primaryKey = 'title';

    protected $with = [
        'dlvry_type',
        'status',
        'dc_version',
        'dc_role',
        'type'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $dlvryType = EnumValue::where('type', 'dc_dlvry_type')->inRandomOrder()->first();
        $status    = EnumValue::where('type', 'active_inactive')->inRandomOrder()->first();
        $dcVersion = EnumValue::where('type', 'delivery_chain_version')->inRandomOrder()->first();
        $dcRole    = EnumValue::where('type', 'delivery_chain_role')->inRandomOrder()->first();
        $type      = DeliveryChainType::inRandomOrder()->first();

        return [
            'title'                => $this->faker()->word(),
            'patch_directory_name' => $this->faker()->word(),
            'dlvry_type'           => $dlvryType->toArray(),
            'status'               => $status->toArray(),
            'dc_version'           => $dcVersion->toArray(),
            'dc_role'              => $dcRole->toArray(),
            'type'                 => $type->toArray()
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
        $data['patch_directory_name'] = $this->faker()->randomNumber();

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
        $data['patch_directory_name'] = 'UPDATED_PATH';

        return $data;
    }
}
