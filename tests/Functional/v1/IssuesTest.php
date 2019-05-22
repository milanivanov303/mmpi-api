<?php

use Modules\Instances\Models\Instance;
use Modules\Projects\Models\Project;

class IssuesTest extends RestTestCase
{
    protected $uri        = 'v1/issues';
    protected $table      = 'issues';
    protected $primaryKey = 'tts_id';

    protected $with = [
        'project',
        'dev_instance',
        'parent_issue'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $instance = Instance::inRandomOrder()->first();
        $project  = Project::inRandomOrder()->first();

        return [
            'subject'           => $this->faker()->realText(),
            'tts_id'            => "TEST-1",
            'jiraissue_id'      => $this->faker()->numberBetween(),
            'created_on'        => $this->faker()->date('Y-m-d H:i:s'),
            'priority'          => $this->faker()->text(10),
            'jira_admin_status' => $this->faker()->randomElement(["ok", "migr", "herrors", "moved", "deleted"]),
            'project'           => $project->toArray(),
            'dev_instance'      => $instance->toArray(),
            'parent_issue'      => null
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
        $data['project']      = 'INVALID_PROJECT';
        $data['dev_instance'] = 'INVALID_DEV_INSTANCE';

        // remove required parameters
        unset($data['jiraissue_id']);

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
        $data['subject'] = 'UPDATED_SUBJECT';

        return $data;
    }
}
