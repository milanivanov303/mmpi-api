<?php

namespace Modules\InstanceDowntimes\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\InstanceDowntimes\Models\InstanceDowntime;
use Modules\Instances\Models\Instance;

class InstanceDowntimeRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * InstanceDowntimeRepository constructor
     *
     * @param InstanceDowntime $model
     */
    public function __construct(InstanceDowntime $model)
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
            'instance' => function ($builder, $value, $operator) {
                return $builder->whereHas('instance', function ($query) use ($value, $operator) {
                    $query->where('name', $operator, $value);
                });
            }
        ];
    }

    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('instance', $data)) {
            $this->model->instance()->associate(
                app(Instance::class)->getModelId($data['instance'], 'name')
            );
        }

        $this->model->madeBy()->associate(Auth::user());

        $this->model->made_on = Carbon::now()->format('Y-m-d H:i:s');
    }
}
