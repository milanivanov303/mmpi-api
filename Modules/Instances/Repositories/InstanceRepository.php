<?php

namespace Modules\Instances\Repositories;

use App\Models\EnumValue;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\DeliveryChains\Models\DeliveryChainType;
use Modules\Instances\Models\Instance;

class InstanceRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectRepository constructor
     *
     * @param Instance $model
     */
    public function __construct(Instance $model)
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
            'owner' => function ($builder, $value, $operator) {
                return $builder->whereHas('owner', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'status' => function ($builder, $value, $operator) {
                return $builder->whereHas('status', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'environment_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('environmentType', function ($query) use ($value, $operator) {
                    $query->where('type', $operator, $value);
                });
            },
            'instance_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('instanceType', function ($query) use ($value, $operator) {
                    $query->where('id', $operator, $value);
                });
            },
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

        if (array_key_exists('owner', $data)) {
            $this->model->owner()->associate(
                app(EnumValue::class)
                    ->getModelId($data['owner'], 'key', ['type' => 'instances_owner'])
            );
        }

        if (array_key_exists('owner', $data)) {
            $this->model->status()->associate(
                app(EnumValue::class)
                    ->getModelId($data['status'], 'key', ['type' => 'active_inactive'])
            );
        }

        if (array_key_exists('environment_type', $data)) {
            $this->model->environmentType()->associate(
                app(DeliveryChainType::class)->getModelId($data['environment_type'], 'title')
            );
        }

        if (array_key_exists('instance_type', $data)) {
            $this->model->instanceType()->associate($data['instance_type']['id'] ?? null);
        }
    }
}
