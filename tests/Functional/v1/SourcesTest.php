<?php

class SourcesTest extends RestTestCase
{
    protected $uri        = 'v1/sources';
    protected $table      = 'source';
    protected $primaryKey = 'source_id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        return [
            'source_name'             => $this->faker()->name(100),
            'source_path'             => $this->faker()->text(200),
            'source_status'           => $this->faker()->numberBetween(0, 2),
            'comment'                 => $this->faker()->text(50),
            'department_id'           => $this->faker()->randomNumber(),
            'dependencies'            => $this->faker()->numberBetween(0,1),
            'library'                 => $this->faker()->numberBetween(0,1)
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
        $data['source_status'] = $this->faker()->text(200);

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
        $data['comment'] = 'UPDATED_COMMENT';

        //Remove date as it is overwritten on each request
        unset($data['department_assigned_on']);

        return $data;
    }
}
