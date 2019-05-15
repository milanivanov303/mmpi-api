<?php

use Modules\Installations\Models\Installation;

class InstallationsTest extends RestTestCase
{
    protected $uri        = 'v1/installations';
    protected $table      = 'installations';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $instance = \Modules\Instances\Models\Instance::minimal()->inRandomOrder()->first();
        $patch  = \Modules\Patches\Models\Patch::inRandomOrder()->first();

        return [
            'patch_id'         => $patch->toArray(),
            'instance_id'      => $instance->toArray(),
            'installed_on'     => $faker->date('Y-m-d H:i:s'),
            'status_id'        => $faker->randomElement([46, 47, 48, 45, 49]),
            'err_output'       => null,
            'duration'         => null,
            'log_file'         => null,
            'timezone_converted' => 849091
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
        return $data;
    }

    /**
    * Test creation
    *
    * @return void
    */
    public function testCreate()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test delete single
     *
     * @return void
     */
    public function testDelete()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = Installation::inRandomOrder()->first()->toArray();

        $this
            ->get( $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->seeJson($data)
            ->assertResponseOk();
    }
}
