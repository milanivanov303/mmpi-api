<?php

use Modules\Projects\Models\Project;

class CertificatesTest extends RestTestCase
{
    protected $uri        = 'v1/certificates';
    protected $table      = 'imx_certificates';
    protected $primaryKey = 'id';
    
    protected $with = [
        'project'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $project  = Project::inRandomOrder()->first();

        return [
            'project'           => $project->toArray(),
            'hash'              => $this->faker()->randomAscii(),
            'organization_name' => $this->faker()->name(),
            'valid_from'        => $this->faker()->dateTime()->format('Y-m-d H:i:s'),
            'valid_to'          => $this->faker()->dateTime()->format('Y-m-d H:i:s')
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
        $data['organization_name'] = $this->faker()->randomNumber();

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
