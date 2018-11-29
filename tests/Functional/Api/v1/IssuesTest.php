<?php

class IssuesTest extends RestTestCase
{
    protected $uri        = 'api/v1/issues';
    protected $table      = 'issues';
    protected $primaryKey = 'tts_id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $instance = \Modules\Instances\Models\Instance::inRandomOrder()->first();
        $project  = \Modules\Projects\Models\Project::inRandomOrder()->first();

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
     * Get request invalid data
     *
     * @return array
     */
    protected function getInvalidData($data)
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
     * @return array
     */
    protected function getUpdateData($data)
    {
        // Change parameters
        $data['subject'] = 'UPDATED_SUBJECT';

        return $data;
    }
}
