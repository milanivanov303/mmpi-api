<?php

use App\Models\EnumValue;
use Modules\ProjectEvents\Models\ProjectEvent;
use Modules\Projects\Models\Project;

class ProjectEventsTest extends RestTestCase
{
    protected $uri        = 'v1/project-events';
    protected $table      = 'project_events';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $project             = Project::inRandomOrder()->first();
        $projectEventType    = EnumValue::where('type', 'project_event_type')->inRandomOrder()->first();
        $projectEventSubtype = EnumValue::where('type', 'project_event_subtype')->inRandomOrder()->first();
        $projectEventStatus  = EnumValue::where('type', 'project_event_status')->inRandomOrder()->first();
        $estimations         = [
            ['duration' => 3, 'department_id'  => 1],
            ['duration' => 24, 'department_id' => 2],
            ['duration' => 36, 'department_id' => 3]
        ];

        return [
            'project'                   => $project->toArray(),
            'project_event_type'        => $projectEventType->toArray(),
            'project_event_subtype'     => $projectEventSubtype->toArray(),
            'event_start_date'          => $this->faker()->date('Y-m-d'),
            'event_end_date'            => $this->faker()->date('Y-m-d'),
            'description'               => $this->faker()->text(59),
            'project_event_status'      => $projectEventStatus->toArray(),
            'project_event_estimations' => $estimations
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
        $data['project_event_type'] = $this->faker()->randomNumber();

        // remove required parameters
        unset($data['project_event_status']);

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
        unset($data['made_on']);
        $data['event_start_date'] = $this->faker()->date('Y-m-d');
        $data['project_event_estimations'] = [
            ['duration' => 3, 'department_id'  => 1],
            ['duration' => 2, 'department_id' => 3]
        ];
        
        return $data;
    }
}
