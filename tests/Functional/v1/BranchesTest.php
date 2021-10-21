<?php

use App\Models\EnumValue;

class BranchesTest extends RestTestCase
{
    protected $uri        = 'v1/branches';
    protected $table      = 'hash_branches';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $repoType = EnumValue::where('type', 'repository_type')->inRandomOrder()->first();

        return [
            'name'               => $this->faker->realText(200),
            'repo_type'          => $repoType->toArray(),
            'description'        => $this->faker->realText(60),
            'repo_master_branch' => $this->faker->numberBetween(0, 1)
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
        $data['repo_type'] = $this->faker->realText(59);

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
        //Remove date as it is overwritten on each request
        unset($data['created_at']);

        // Change parameters
        $data['name'] = $this->faker->realText(60);

        return $data;
    }
}
