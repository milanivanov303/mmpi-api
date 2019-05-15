<?php

namespace Modules\Instances\Repositories;

use App\Models\EnumValue;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\DeliveryChains\Models\DeliveryChainType;
use Modules\Instances\Models\Instance;
use Modules\DeliveryChains\Models\DeliveryChain;

class InstanceRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'owner',
        'status',
        'environmentType',
        'instanceType',
        'deliveryChains'
    ];

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
