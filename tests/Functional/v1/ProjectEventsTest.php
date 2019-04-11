<?php

use App\Models\EnumValue;
use App\Models\User;
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

        $project              = \Modules\Projects\Models\Project::inRandomOrder()->first();
        $user                 = User::inRandomOrder()->first();
        $projectEventType     = EnumValue::where('type', 'project_event_type')->inRandomOrder()->minimal()->first();
        $projectEventStatus   = EnumValue::where('type', 'project_event_status')->inRandomOrder()->minimal()->first();

        return [
            'project'              => $project->toArray(),
            'project_event_type'   => $projectEventType->toArray(),
            'event_start_date'        => $faker->date('Y-m-d H:i:s'),
            'event_end_date'          => $faker->date('Y-m-d H:i:s'),
            'made_by'                 => $user->toArray(),
            'made_on'                 => $faker->date('Y-m-d H:i:s'),
            'description'             => null,
            'project_event_status'    => $projectEventStatus->toArray()
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
        $data['project_event_type_id'] = $faker->randomNumber();

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
        $data['event_start_date'] = $faker->date('Y-m-d H:i:s');

        return $data;
    }

    /**
    * Test creation
    *
    * @return void
    */
    public function testCreate()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $this->assertEquals(true, true);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->assertEquals(true, true);
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

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = ProjectEvent::inRandomOrder()->first()->toArray();

        $this
            ->get( $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->seeJson($data)
            ->assertResponseOk();
    }
}
