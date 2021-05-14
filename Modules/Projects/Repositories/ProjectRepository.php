<?php

namespace Modules\Projects\Repositories;

use App\Models\EnumValue;
use App\Models\User;
use Core\Models\Model;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Projects\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Custom unique primaryKey
     *
     * @var array
     */
    protected $customUniqueKey = 'name';

    /**
     * ProjectRepository constructor
     *
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'delivery_chains_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('deliveryChains', function ($query) use ($value, $operator) {
                    $query->whereHas('type', function ($query) use ($value, $operator) {
                        $query->where('type', $operator, $value);
                    });
                });
            }
        ];
    }

    /**
     * Fill model attributes
     *
     * @param array $data
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        $this->model->modifiedBy()->associate(Auth::user());

        if (array_key_exists('group', $data)) {
            $this->model->group()->associate(
                app(EnumValue::class)
                    ->getModelId($data['group'], 'key', ['type' => 'project_groups'])
            );
        }

        if (array_key_exists('project_type', $data)) {
            $this->model->projectType()->associate(
                app(EnumValue::class)
                    ->getModelId($data['project_type'], 'key', ['type' => 'project_type'])
            );
        }

        if (array_key_exists('country', $data)) {
            $this->model->country()->associate(
                app(EnumValue::class)
                    ->getModelId($data['country'], 'key', ['type' =>'country'])
            );
        }

        if (array_key_exists('communication_lng', $data)) {
            $this->model->communicationLng()->associate(
                app(EnumValue::class)
                    ->getModelId($data['communication_lng'], 'key', ['type' => 'communication_language'])
            );
        }

        if (array_key_exists('delivery_method', $data)) {
            $this->model->deliveryMethod()->associate(
                app(EnumValue::class)
                    ->getModelId($data['delivery_method'], 'key', ['type' => 'delivery_method'])
            );
        }

        if (array_key_exists('se_mntd_by_clnt', $data)) {
            $this->model->seMntdByClnt()->associate(
                app(EnumValue::class)
                    ->getModelId($data['se_mntd_by_clnt'], 'key', ['type' => 'project_specific_feature'])
            );
        }

        if (array_key_exists('tl_mntd_by_clnt', $data)) {
            $this->model->tlMntdByClnt()->associate(
                app(EnumValue::class)
                    ->getModelId($data['tl_mntd_by_clnt'], 'key', ['type' => 'project_specific_feature'])
            );
        }

        if (array_key_exists('njsch_mntd_by_clnt', $data)) {
            $this->model->njschMntdByClnt()->associate(
                app(EnumValue::class)
                    ->getModelId($data['njsch_mntd_by_clnt'], 'key', ['type' => 'project_specific_feature'])
            );
        }

        if (array_key_exists('trans_mntd_by_clnt', $data)) {
            $this->model->transMntdByClnt()->associate(
                app(EnumValue::class)
                    ->getModelId($data['trans_mntd_by_clnt'], 'key', ['type' => 'project_specific_feature'])
            );
        }

        if (array_key_exists('extranet_version', $data)) {
            $this->model->extranetVersion()->associate(
                app(EnumValue::class)
                    ->getModelId(
                        $data['extranet_version'],
                        'key',
                        ['type' => 'delivery_chain_version','subtype' => 'EXTRANET']
                    )
            );
        }

        if (array_key_exists('intranet_version', $data)) {
            $this->model->intranetVersion()->associate(
                app(EnumValue::class)
                    ->getModelId(
                        $data['intranet_version'],
                        'key',
                        ['type' => 'delivery_chain_version', 'subtype' => 'IMX']
                    )
            );
        }
    }

    /**
     * Save record
     *
     * @param array $data
     * @return Model
     *
     * @throws \Throwable
     */
    protected function save($data)
    {
        $this->fillModel($data);

        $this->model->saveOrFail();

        if (array_key_exists('delivery_chains', $data)) {
            $deliveryChains = [];
            foreach ($data['delivery_chains'] as $deliveryChain) {
                $deliveryChains[] = app(DeliveryChain::class)->getModelId($deliveryChain, 'title');
            }
            $this->model->deliveryChains()->sync($deliveryChains);
        }

        if (array_key_exists('languages', $data)) {
            $languages = [];
            foreach ($data['languages'] as $language) {
                $enumValue = app(EnumValue::class)->findModel(
                    $language,
                    'key',
                    [
                        'type'    => 'project_specific_feature',
                        'subtype' => 'project_appl_language'
                    ]
                );

                if ($enumValue) {
                    $languages[$enumValue->id] = [
                        'made_by' => Auth::user()->id,
                        'comment' => $enumValue->description,
                        'value'   => $language['priority'] ?? null
                    ];
                }
            }

            if ($this->model->languages->count() > 0) {
                $oldLanguages = array_map(function ($a) {
                    return $a['id'];
                }, $this->model->languages->toArray());

                $this->model->languages()->detach($oldLanguages);
            }

            $this->model->languages()->sync($languages, false);
        }

        if (array_key_exists('type_business', $data)) {
            $businesses = [];
            foreach ($data['type_business'] as $business) {
                $enumValue = app(EnumValue::class)->findModel(
                    $business,
                    'key',
                    [
                        'type'    => 'project_specific_feature',
                        'subtype' => 'imx_activity'
                    ]
                );

                if ($enumValue) {
                    $businesses[$enumValue->id] = [
                        'made_by' => Auth::user()->id,
                        'comment' => $enumValue->description
                    ];
                }
            }

            if ($this->model->typeBusiness->count() > 0) {
                $oldBusinesses = array_map(function ($a) {
                    return $a['id'];
                }, $this->model->typeBusiness->toArray());

                $this->model->typeBusiness()->detach($oldBusinesses);
            }

            $this->model->typeBusiness()->sync($businesses, false);
        }

        $this->loadModelRelations($data);

        return $this->model;
    }

    /**
     * Delete record
     *
     * @param mixed $id
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $model = $this->find($id);
            $model->deliveryChains()->sync([]);
            $model->projectSpecifics()->delete();

            $model->delete();
        });
    }
}
