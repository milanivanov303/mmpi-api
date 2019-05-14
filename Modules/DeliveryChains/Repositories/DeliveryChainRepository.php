<?php

namespace Modules\DeliveryChains\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Projects\Models\Project;
use Modules\Instances\Models\Instance;

class DeliveryChainRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'title';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'type',
        'dlvryType',
        'status',
        'dcVersion',
        'dcRole',
        'projects',
        'instances'
    ];

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
     * Save record
     *
     * @param array $data
     * @return Model
     * @throws \Throwable
     */
    protected function save($data)
    {
        $this->model->type()->associate($data['type']['id']);
        $this->model->dlvryType()->associate($data['dlvry_type']['id']);

        // seting associate on relations with same name as column fails!!!
        //$this->model->status()->associate($data['status']['id']);
        $this->model->status = $data['status']['id'];

        $this->model->dcVersion()->associate(isset($data['dc_version']) ? $data['dc_version']['id'] : null);
        $this->model->dcRole()->associate(isset($data['dc_role']) ? $data['dc_role']['id'] : null);

        $this->model->fill($data)->saveOrFail();

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
        $model->projects()->sync([]);
        $model->instances()->sync([]);

        return $model->delete();
    }
}
