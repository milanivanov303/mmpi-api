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

class ProjectRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'name';

     /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'modifiedBy',
        'typeBusiness',
        'group',
        'country',
        'communicationLng',
        'deliveryMethod',
        'seMntdByClnt',
        'tlMntdByClnt',
        'njschMntdByClnt',
        'transMntdByClnt',
        'deliveryChains',
        'activity'
    ];

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

        if (array_key_exists('type_business', $data)) {
            $this->model->typeBusiness()->associate(
                app(EnumValue::class)
                    ->getModelId($data['type_business'], 'key', ['type' => 'type_business'])
            );
        }

        if (array_key_exists('group', $data)) {
            $this->model->group()->associate(
                app(EnumValue::class)
                    ->getModelId($data['group'], 'key', ['type' => 'project_groups'])
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

        $this->model->load($this->getWith());

        return $this->model;
    }

    /**
     * Delete record
     *
     * @param mixed $id
     * @return boolean
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        $model = $this->find($id);
        $model->deliveryChains()->sync([]);

        return $model->delete();
    }
}
