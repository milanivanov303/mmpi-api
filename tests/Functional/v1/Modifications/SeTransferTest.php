<?php

use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Instances\Models\Instance;
use Modules\Issues\Models\Issue;
use App\Models\Department;

class SeTransferTest extends RestTestCase
{
    protected $uri        = 'v1/modifications/se-transfers';
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
        $issue = Issue::inRandomOrder()->first();
        $subtype = EnumValue::where('subtype', 'SE')->inRandomOrder()->first();
        $deliveryChain = DeliveryChain::inRandomOrder()->first();
        $instance = Instance::inRandomOrder()->first();
        $instanceStatus = EnumValue::where('type', 'instance_status')->inRandomOrder()->first();
        $creatorDepartment = Department::inRandomOrder()->first();

        return [
            'issue_id'           => $issue->id,
            'type_id'            => $this->typeId,
            'subtype_id'         => $subtype->id,
            'delivery_chain'     => $deliveryChain->toArray(),
            'instance'           => $instance->toArray(),
            'instance_status'    => $instanceStatus->toArray(),
            'active'             => $this->faker->numberBetween(0, 1),
            'visible'            => $this->faker->numberBetween(0, 1),
            'creator_department' => $creatorDepartment->toArray()
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
        $data['active'] = $this->faker->realText(59);

        return $data;
    }

    /**
     * Test create
     *
     * @return void
     */
    public function testCreate()
    {
        $data = $this->getData();

        $this
            ->json('POST', $this->uri . '/' . $this->getPrimaryKeyValue($data), $data)
            ->assertResponseStatus(201);
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
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = $this->getData();

        $this
            ->json('GET', $this->uri . '/' . $this->getPrimaryKeyValue($data), [
                'with' => $this->getWith($data)
            ])
            ->assertResponseOk();
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
