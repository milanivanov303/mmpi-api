<?php

use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Issues\Models\Issue;
use App\Models\Department;
use App\Models\User;

class BinariesTest extends RestTestCase
{
    protected $uri        = 'v1/modifications/binaries';
    protected $table      = 'modifications';
    protected $primaryKey = 'id';
    protected $typeId     = 'binary';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $issue = Issue::inRandomOrder()->first();
        $subtype = EnumValue::where('key', 'maven')->inRandomOrder()->first();
        $deploymentPrefix = EnumValue::where('key', 'source_path_imx_tmp')->inRandomOrder()->first();
        $deliveryChain = DeliveryChain::inRandomOrder()->first();
        $instanceStatus = EnumValue::where('type', 'instance_status')->inRandomOrder()->first();
        $creatorDepartment = Department::inRandomOrder()->first();
        $createdBy = User::inRandomOrder()->first();
        
        return [
            'issue'              => $issue->toArray(),
            'type_id'            => $this->typeId,
            'subtype'            => $subtype->toArray(),
            'version'            => $this->faker->realText(32),
            'revision_converted' => $this->faker->realText(100),
            'name'               => $this->faker->realText(250),
            'maven_repository'   => $this->faker->realText(256),
            'deployment_prefix'  => $deploymentPrefix->toArray(),
            'delivery_chain'     => $deliveryChain->toArray(),
            'instance_status'    => $instanceStatus->toArray(),
            'active'             => $this->faker->numberBetween(0, 1),
            'visible'            => $this->faker->numberBetween(0, 1),
            'creator_department' => $creatorDepartment->toArray(),
            'created_by_id'      => $createdBy->toArray()
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
