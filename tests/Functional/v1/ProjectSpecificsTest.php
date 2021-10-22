<?php

use App\Models\EnumValue;
use Modules\Projects\Models\Project;

class ProjectSpecificsTest extends RestTestCase
{
    protected $uri        = 'v1/project-specifics';
    protected $table      = 'project_specifics';
    protected $primaryKey = 'id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $project                = Project::inRandomOrder()->first();
        $projectSpecificFeature = EnumValue::where('type', 'project_specific_feature')->inRandomOrder()->first();

        return [
            'project'                  => $project->toArray(),
            'project_specific_feature' => $projectSpecificFeature->toArray(),
            'value'                    => $this->faker->randomNumber(),
            'date_characteristic'      => $this->faker->date('Y-m-d'),
            'comment'                  => $this->faker->realText(59)
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
        $data['value'] = $this->faker->realText(59);

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
        $data['date_characteristic'] = $faker->date('Y-m-d');

        return $data;
    }
}
