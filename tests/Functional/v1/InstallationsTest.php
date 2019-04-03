<?php

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

        $instance = \Modules\Instances\Models\Instance::inRandomOrder()->first();
        $patch  = \Modules\Patches\Models\Patch::inRandomOrder()->first();

        return [
            'id'               => $faker->number(),
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
}
