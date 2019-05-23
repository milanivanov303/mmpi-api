<?php

use App\Models\EnumValue;

class ProjectSpecificsTest extends RestTestCase
{
    protected $uri        = 'v1/project-specifics';
    protected $table      = 'project_specifics';
    protected $primaryKey = 'id';

    protected $with = [
        'made_by',
        'project_specific_feature',
        'project'
    ];

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $project                = \Modules\Projects\Models\Project::inRandomOrder()->first();
        $projectSpecificFeature = EnumValue::inRandomOrder()->first();

        return [
            'project'                  => $project->toArray(),
            'project_specific_feature' => $projectSpecificFeature->toArray(),
            'value'                    => $faker->randomNumber(),
            'date_characteristic'      => $faker->date('Y-m-d'),
            'comment'                  => $faker->text(59)
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
        $data['value'] = $faker->text(59);

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
