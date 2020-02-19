<?php

use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Issues\Models\Issue;

class BinariesTest extends RestTestCase
{
    protected $uri        = 'v1/modifications/se-transfer';
    protected $table      = 'modifications';
    protected $primaryKey = 'id';
    protected $typeId     = 'se';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $issue = Issue::inRandomOrder()->first();
        $subtype = EnumValue::where('key', 'SE')->inRandomOrder()->first();
        $deliveryChain = DeliveryChain::inRandomOrder()->first();
        $instance = Instance::inRandomOrder()->first();
        $instanceStatus = EnumValue::where('type', 'instance_status')->inRandomOrder()->first();
        
        return [
            'issue'              => $issue->toArray(),
            'type_id'            => $this->typeId,
            'subtype'            => $subtype->toArray(),
            'delivery_chain'     => $deliveryChain->toArray(),
            'instance'           => $instance->toArray(),
            'instance_status'    => $instanceStatus->toArray(),
            'active'             => $faker->numberBetween(0, 1)
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
        $data['active'] = $faker->text(59);

        return $data;
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
