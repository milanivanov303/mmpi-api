<?php

use App\Models\EnumValue;
use Modules\DeliveryChains\Models\DeliveryChain;

class ProjectsTest extends RestTestCase
{
    protected $uri        = 'v1/projects';
    protected $table      = 'projects';
    protected $primaryKey = 'name';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $faker = Faker\Factory::create();

        $typeBusiness     = EnumValue::where('type', 'type_business')->inRandomOrder()->first();
        $group            = EnumValue::where('type', 'project_groups')->inRandomOrder()->first();
        $country          = EnumValue::where('type', 'country')->inRandomOrder()->first();
        $communicationLng = EnumValue::where('type', 'communication_language')->inRandomOrder()->first();
        $deliveryMethod   = EnumValue::where('type', 'delivery_method')->inRandomOrder()->first();
        $seMntdByClnt     = EnumValue::where('type', 'project_specific_feature')->inRandomOrder()->first();
        $tlMntdByClnt     = EnumValue::where('type', 'project_specific_feature')->inRandomOrder()->first();
        $deliveryChains   = DeliveryChain::without('projects')->active()->inRandomOrder()->limit(3)->get();

        return [
            'name'               => $faker->text(128),
            'clnt_cvs_dir'       => $faker->text(32),
            'pnp_type'           => $faker->text(32),
            'clnt_code'          => $faker->text(16),
            'clnt_code2'         => $faker->text(16),
            'src_prefix'         => $faker->text(8),
            'src_prefix2'        => null,
            'src_itf_prefix'     => null,
            'getdcli'            => $faker->text(16),
            'getdcli2'           => $faker->text(16),
            'activity'           => null,
            'activite_gpc'       => null,
            'activite_sdr'       => "o",
            'imx_formstag'       => null,
            'forms_lng_dlvry'    => 1,
            'uses_transl_upd'    => 0,
            'inactive'           => 0,
            'display_name'       => $faker->text(128),
            'sla_from'           => $faker->time(),
            'sla_to'             => $faker->time(),
            'type_business'      => $typeBusiness->toArray(),
            'group'              => $group->toArray(),
            'country'            => $country->toArray(),
            'communication_lng'  => $communicationLng->toArray(),
            'delivery_method'    => $deliveryMethod->toArray(),
            'se_mntd_by_clnt'    => $seMntdByClnt->toArray(),
            'tl_mntd_by_clnt'    => $tlMntdByClnt->toArray(),
            'njsch_mntd_by_clnt' => null,
            'trans_mntd_by_clnt' => null,
            'delivery_chains'    => $deliveryChains->toArray()
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
        $data['country'] = $faker->randomNumber();

        // remove required parameters
        unset($data['name']);

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
        $data['display_name'] = 'UPDATED_DISPLAY_NAME';

        return $data;
    }
}
