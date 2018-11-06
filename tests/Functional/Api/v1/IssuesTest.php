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
     * Test creation of issue
     *
     * @return void
     */
    public function testCreateIssue()
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
     * Test creation of issue with wrong data
     *
     * @return void
     */
    public function testCreateIssueWithInvalidData()
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
     * Test get single issue
     *
     * @return void
     */
    public function testGetIssue()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->get( $this->uri . '/' . $data[$this->primaryKey])
            ->seeJson($data)
            ->assertResponseOk();
    }

    /**
     * Test get non existing issue
     *
     * @return void
     */
    public function testGetNonExistingIssue()
    {
        $this
            ->get($this->uri . '/NON-EXISTING-ISSUE')
            ->assertResponseStatus(404);
    }

    /**
     * Test update of issue
     *
     * @return void
     */
    public function testUpdateIssue()
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
     * Test get single issue
     *
     * @return void
     */
    public function testDeleteIssue()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->json('DELETE', $this->uri . '/' . $data['tts_id'])
            ->assertResponseStatus(204);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]]
        );
    }

    /**
     * Test get issue list
     *
     * @return void
     */
    public function testGetIssuesList()
    {
        $this
            ->json('GET', $this->uri . '?limit=100')
            ->shouldReturnJson()
            ->seeJsonStructure(['data'])
            ->assertResponseOk();
    }


    /**
     * Test get paginated issues list
     *
     * @return void
     */
    public function testGetPaginatedIssuesList()
    {
        $this
            ->json('GET', $this->uri . '?page=3')
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
