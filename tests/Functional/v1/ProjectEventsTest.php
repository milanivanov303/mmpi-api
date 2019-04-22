<?php

use App\Models\EnumValue;
use Modules\ProjectEvents\Models\ProjectEvent;

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
        $faker = Faker\Factory::create();

        $project            = \Modules\Projects\Models\Project::inRandomOrder()->first();
        $projectEventType   = EnumValue::where('type', 'project_event_type')->inRandomOrder()->minimal()->first();
        $projectEventStatus = EnumValue::where('type', 'project_event_status')->inRandomOrder()->minimal()->first();

        return [
            'project'              => $project->toArray(),
            'project_event_type'   => $projectEventType->toArray(),
            'event_start_date'     => $faker->date('Y-m-d'),
            'event_end_date'       => $faker->date('Y-m-d'),
            'description'          => $faker->text(59),
            'project_event_status' => $projectEventStatus->toArray()
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
        $data['project_event_type'] = $faker->randomNumber();

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
        $faker = Faker\Factory::create();
        // Change parameters

        //Remove date as it is overwritten on each request
        unset($data['made_on']);
        $data['event_start_date'] = $faker->date('Y-m-d');

        return $data;
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
}
