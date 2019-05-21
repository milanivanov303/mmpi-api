<?php

use App\Models\EnumValue;

class BranchesTest extends RestTestCase
{
    protected $uri        = 'v1/branches';
    protected $table      = 'hash_branches';
    protected $primaryKey = 'id';

    protected $with = [
        'repo_type'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $repoType = EnumValue::where('type', 'repository_type')->inRandomOrder()->first();
        
        return [
            'name'               => $faker->text(200),
            'repo_type'          => $repoType->toArray(),
            'description'        => $faker->text(60),
            'repo_master_branch' => $faker->numberBetween(0, 1)
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
        $data['repo_type'] = $faker->text(59);

        // remove required parameters
        unset($data['name']);

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
        $faker = Faker\Factory::create();
        
        //Remove date as it is overwritten on each request
        unset($data['created_at']);

        // Change parameters
        $data['name'] = $faker->text(60);

        return $data;
    }
}
