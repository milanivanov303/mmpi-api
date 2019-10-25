<?php

use App\Models\EnumValue;
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

        return [
            'project'               => $project->toArray(),
            'project_event_type'    => $projectEventType->toArray(),
            'project_event_subtype' => $projectEventSubtype->toArray(),
            'event_start_date'      => $this->faker()->date('Y-m-d'),
            'event_end_date'        => $this->faker()->date('Y-m-d'),
            'description'           => $this->faker()->text(59),
            'project_event_status'  => $projectEventStatus->toArray()
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
        unset($data['project']);

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

        return $data;
    }
}
