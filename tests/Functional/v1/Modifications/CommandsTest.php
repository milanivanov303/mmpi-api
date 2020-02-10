<?php

use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Instances\Models\Instance;
use Modules\Issues\Models\Issue;

class CommandsTest extends RestTestCase
{
    protected $uri        = 'v1/modifications/commands';
    protected $table      = 'modifications';
    protected $primaryKey = 'id';
    protected $typeId     = 'cmd';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $issue = Issue::inRandomOrder()->first();
        $subtype = EnumValue::where('key', 'maven')->inRandomOrder()->first();
        $deliveryChain = DeliveryChain::inRandomOrder()->first();
        $instanceStatus = EnumValue::where('type', 'instance_status')->inRandomOrder()->first();
        $instance = Instance::inRandomOrder()->first();

        return [
            'type_id'            => $this->typeId,
            'issue'              => $issue->toArray(),
            'delivery_chain'     => $deliveryChain->toArray(),
            'name'               => $faker->text(2500),
            'comments'           => $faker->text(250),
            'check_exit_status'  => $faker->numberBetween(0, 1),
            'subtype'            => $subtype->toArray(),
            'instance'           => $instance->toArray(),
            'instance_status'    => $instanceStatus->toArray(),
            'est_run_time'       => $faker->time('H:i:s'),
            'visible'            => $faker->numberBetween(0, 1)
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
        $data['est_run_time'] = $faker->text(59);

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
