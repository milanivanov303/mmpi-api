<?php

use Modules\Departments\Models\Department;
use Modules\ProjectEvents\Models\ProjectEvent;

class ProjectEventEstimationsTest extends RestTestCase
{
    protected $uri        = 'v1/project-event-estimations';
    protected $table      = 'project_event_estimations';
    protected $primaryKey = 'id';

    protected $with = [
        'project_event',
        'department'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $projectEvent = ProjectEvent::inRandomOrder()->first();
        $department   = Department::inRandomOrder()->first();

        return [
            'project_event' => $projectEvent->toArray(),
            'department'    => $department->toArray(),
            'duration'      => $this->faker()->randomNumber()
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
        unset($data['project_event']);

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
        $data['duration'] = $this->faker()->randomNumber();

        return $data;
    }
}
