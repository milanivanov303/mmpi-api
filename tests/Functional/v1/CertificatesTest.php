<?php

class CertificatesTest extends RestTestCase
{
    protected $uri        = 'v1/certificates';
    protected $table      = 'imx_certificates';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $project  = \Modules\Projects\Models\Project::inRandomOrder()->first();

        return [
            'project'           => $project->toArray(),
            'hash'              => $faker->randomAscii(),
            'organization_name' => $faker->name(),
            'valid_from'        => $faker->dateTime()->format('Y-m-d H:i:s'),
            'valid_to'          => $faker->dateTime()->format('Y-m-d H:i:s')
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
        $data['organization_name'] = $faker->randomNumber();

        // remove required parameters
        unset($data['hash']);

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
        $data['organization_name'] = 'UPDATED ORGANIZATION NAME';

        return $data;
    }
}
