<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IssuesTest extends TestCase
{
    use DatabaseTransactions;

    protected $uri        = 'api/v1/issues';
    protected $table      = 'issues';
    protected $primaryKey = 'tts_id';

    public function setUp() {
        parent::setUp();
        $this->actingAs(User::first());
    }

    /**
     * Get request data
     *
     * @return array
     */
    public function getData()
    {
        $faker = Faker\Factory::create();

        $instance = \App\Modules\Instances\Models\Instance::inRandomOrder()->first();
        $project  = \App\Modules\Projects\Models\Project::inRandomOrder()->first();

        return [
            'subject'           => $faker->realText(),
            'tts_id'            => "TEST-1",
            'jiraissue_id'      => $faker->numberBetween(),
            'created_on'        => $faker->date('Y-m-d H:i:s'),
            'priority'          => $faker->text(10),
            'jira_admin_status' => $faker->randomElement(["ok", "migr", "herrors", "moved", "deleted"]),
            'project'           => $project->toArray(),
            'dev_instance'      => $instance->toArray(),
            'parent_issue'      => null
        ];
    }

    /**
     * Test creation
     *
     * @return void
     */
    public function testCreate()
    {
        $data = $this->getData();

        $this
            ->json('POST', $this->uri, $data)
            ->seeJson($data)
            ->assertResponseStatus(201);

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]
        ]);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $data = $this->getData();

        // Set invalid parameters
        $data['project']      = 'INVALID_PROJECT';
        $data['dev_instance'] = 'INVALID_DEV_INSTANCE';

        // remove required parameters
        unset($data['jiraissue_id']);

        $this
            ->json('POST', $this->uri, $data)
            ->seeJsonStructure(['project', 'dev_instance', 'jiraissue_id'])
            ->assertResponseStatus(422);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]
        ]);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->get( $this->uri . '/' . $data[$this->primaryKey])
            ->seeJson($data)
            ->assertResponseOk();
    }

    /**
     * Test get non existing
     *
     * @return void
     */
    public function testGetNonExisting()
    {
        $this
            ->get($this->uri . '/NON-EXISTING-ISSUE')
            ->assertResponseStatus(404);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        // Change parameters
        $data['subject'] = 'UPDATED_SUBJECT';

        $this
            ->json('PUT', $this->uri . '/' . $data[$this->primaryKey], $data)
            ->seeJson($data)
            ->assertResponseOk();

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]
        ]);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testDelete()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->json('DELETE', $this->uri . '/' . $data[$this->primaryKey])
            ->assertResponseStatus(204);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]]
        );
    }

    /**
     * Test get list
     *
     * @return void
     */
    public function testGetList()
    {
        $this
            ->json('GET', $this->uri . '?limit=10')
            ->shouldReturnJson()
            ->seeJsonStructure(['data'])
            ->assertResponseOk();
    }


    /**
     * Test get paginated list
     *
     * @return void
     */
    public function testGetPaginatedList()
    {
        $this
            ->json('GET', $this->uri . '?page=3')
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
