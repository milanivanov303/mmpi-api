<?php

class IssuesTest extends RestTestCase
{
    protected $uri        = 'v1/issues';
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

        $instance = \Modules\Instances\Models\Instance::minimal()->inRandomOrder()->first();
        $project  = \Modules\Projects\Models\Project::minimal()->inRandomOrder()->first();

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
