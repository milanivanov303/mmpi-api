<?php

namespace Modules\DeliveryChains\Repositories;

use App\Models\EnumValue;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Projects\Models\Project;
use Modules\Instances\Models\Instance;
use Modules\DeliveryChains\Models\DeliveryChainType;

class DeliveryChainRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Custom unique primaryKey
     *
     * @var array
     */
    protected $customUniqueKey = 'title';

    /**
     * ProjectRepository constructor
     *
     * @param DeliveryChain $model
     */
    public function __construct(DeliveryChain $model)
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
            'type' => function ($builder, $value, $operator) {
                return $builder->whereHas('type', function ($query) use ($value, $operator) {
                    $query->where('type', $operator, $value);
                });
            },
            'status' => function ($builder, $value, $operator) {
                return $builder->whereHas('status', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'dlvry_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('dlvryType', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'dc_version' => function ($builder, $value, $operator) {
                return $builder->whereHas('dcVersion', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'dc_role' => function ($builder, $value, $operator) {
                return $builder->whereHas('dcRole', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            }
        ];
    }

    /**
     * Define order by for this model
     *
     * @return array
     */
    public function orderBy(): array
    {
        return [
            'type' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                    ->join('delivery_chain_types', 'delivery_chain_types.id', '=', 'delivery_chains.type_id')
                    ->orderBy('delivery_chain_types.type', $order_dir);
            },
            'status' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                    ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.status')
                    ->orderBy('enum_values.key', $order_dir);
            },
            'dlvry_type' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                    ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.dlvry_type')
                    ->orderBy('enum_values.key', $order_dir);
            },
            'dc_version' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                    ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.dc_version')
                    ->orderBy('enum_values.key', $order_dir);
            },
            'dc_role' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                    ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.dc_role')
                    ->orderBy('enum_values.key', $order_dir);
            },
        ];
    }

    /**
     * @inheritDoc
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('type', $data)) {
            $this->model->type()->associate(
                app(DeliveryChainType::class)->getModelId($data['type'])
            );
        }

        if (array_key_exists('dlvry_type', $data) && is_array($data['dlvry_type'])) {
            $this->model->dlvryType()->associate(
                app(EnumValue::class)
                    ->getModelId($data['dlvry_type'], 'key', ['type' => 'dc_dlvry_type'])
            );
        }

        if (array_key_exists('status', $data) && is_array($data['status'])) {
            $this->model->status()->associate(
                app(EnumValue::class)
                    ->getModelId($data['status'], 'key', ['type' => 'active_inactive'])
            );
        }

        if (array_key_exists('dc_version', $data) && is_array($data['dc_version'])) {
            $this->model->dcVersion()->associate(
                app(EnumValue::class)
                    ->getModelId($data['dc_version'], 'key', ['type' => 'delivery_chain_version'])
            );
        }

        if (array_key_exists('dc_role', $data) && is_array($data['dc_role'])) {
            $this->model->dcRole()->associate(
                app(EnumValue::class)
                    ->getModelId($data['dc_role'], 'key', ['type' => 'delivery_chain_role'])
            );
        }
    }

    /**
     * Save record
     *
     * @param array $data
     * @return DeliveryChain
     *
     * @throws \Throwable
     */
    protected function save($data)
    {
        $this->fillModel($data);

        $this->model->saveOrFail();

        if (array_key_exists('projects', $data)) {
            $projects = [];
            foreach ($data['projects'] as $project) {
                $projects[] = app(Project::class)->getModelId($project, 'name');
            }
            
            $this->model->projects()->sync($projects);
        }

        if (array_key_exists('instances', $data)) {
            $instances = [];
            foreach ($data['instances'] as $instance) {
                $instances[] = app(Instance::class)->getModelId($instance, 'id');
            }
            
            $this->model->instances()->sync($instances);
        }

        $this->model->load($this->getWith($data));

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
        $model->projects()->sync([]);
        $model->instances()->sync([]);

        return $model->delete();
    }
}
