<?php

use Modules\Instances\Models\Instance;

class InstanceDowntimesTest extends RestTestCase
{
    protected $uri        = 'v1/instance-downtimes';
    protected $table      = 'instance_downtimes';
    protected $primaryKey = 'id';

    protected $with = [
        'instance'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $instance = Instance::inRandomOrder()->first();

        return [
            'instance'       => $instance->toArray(),
            'start_datetime' => $this->faker()->date('Y-m-d H:i:s'),
            'end_datetime'   => $this->faker()->date('Y-m-d H:i:s'),
            'status'         => $this->faker()->numberBetween(0, 1),
            'description'    => $this->faker()->text(60)
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
        // Change parameters
        $data['start_datetime'] = $this->faker()->date('Y-m-d H:i:s');

        // Unset made_on as it is not changed on update and make test invalid
        unset($data['made_on']);

        return $data;
    }
}
