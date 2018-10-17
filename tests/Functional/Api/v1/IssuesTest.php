<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class IssuesTest extends TestCase
{
    use DatabaseTransactions;
    
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

        $instance = \App\Models\Instance::inRandomOrder()->first();
        $project  = \App\Models\Project::inRandomOrder()->first();

        return [
            'subject'           => $faker->realText(),
            'tts_id'            => "TEST-1",
            'jiraissue_id'      => $faker->numberBetween(),
            'created_on'        => $faker->date('Y-m-d H:i:s'),
            'priority'          => $faker->text(10),
            'jira_admin_status' => $faker->randomElement(["ok", "migr", "herrors", "moved", "deleted"]),
            'project'           => $project->name,
            'dev_instance'      => $instance->name,
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
            ->json('POST', '/api/v1/issues', $data)
            ->seeJson($data)
            ->assertResponseStatus(201);
        
        $this->seeInDatabase('issues', ['tts_id' => $data['tts_id']]);
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
            ->json('POST', '/api/v1/issues', $data)
            ->seeJsonStructure(['project', 'dev_instance', 'jiraissue_id'])
            ->assertResponseStatus(422);

        $this->missingFromDatabase('issues', ['tts_id' => $data['tts_id']]);
    }
    
    /**
     * Test get single issue
     * 
     * @return void
     */
    public function testGetIssue()
    {
        $data = $this->getData();

        $this->json('POST', '/api/v1/issues', $data);

        $this
            ->get('/api/v1/issues/' . $data['tts_id'])
            ->seeJson($data)
            ->assertResponseOk();
    }

    /**
     * Test get non existing issue
     *
     * @return void
     */
    public function testGetNonExistingHash()
    {
        $this
            ->get('/api/v1/issue/NON-EXISTING-ISSUE')
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
        
        $this->json('POST', '/api/v1/issues', $data);

        // Change parameters
        $data['subject'] = 'UPDATED_SUBJECT';

        $this
            ->json('PUT', '/api/v1/issues/' . $data['tts_id'], $data)
            ->seeJson($data)
            ->assertResponseOk();
        
        $this->seeInDatabase('issues', ['tts_id' => $data['tts_id']]);
    }

    /**
     * Test get single issue
     *
     * @return void
     */
    public function testDeleteIssue()
    {
        $data = $this->getData();

        $this->json('POST', '/api/v1/issues', $data);

        $this
            ->json('DELETE', '/api/v1/issues/' . $data['tts_id'])
            ->assertResponseStatus(204);

        $this->missingFromDatabase('issues', ['tts_id' => $data['tts_id']]);
    }
    
    /**
     * Test get issue list
     * 
     * @return void
     */
    public function testGetIssuesList()
    {
        $this
            ->json('GET', '/api/v1/issues')
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
            ->json('GET', '/api/v1/issues?page=3')
            ->seeJsonStructure(['meta' => ['pagination' => ['total', 'current_page']], 'data'])
            ->assertResponseOk();
    }
}
