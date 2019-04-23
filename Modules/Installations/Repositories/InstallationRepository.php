<?php

namespace Modules\Installations\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Installations\Models\Installation;
use Modules\Instances\Models\Instance;
use Modules\Patches\Models\Patch;
use App\Models\EnumValue;

class InstallationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * InstallationRepository constructor
     *
     * @param Installation $model
     */
    public function __construct(Installation $model)
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
            'status' => function ($builder, $value, $operator) {
                return $builder->whereHas('status', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            }
        ];
    }

    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('patch', $data)) {
            $this->model->patch()->associate(
                app(Patch::class)->getModelId($data['patch'], 'dlv_file_name')
            );
        }

        if (array_key_exists('instance', $data)) {
            $this->model->instance()->associate(
                app(Instance::class)->getModelId($data['instance'], 'id')
            );
        }

        if (array_key_exists('status', $data)) {
            $this->model->status()->associate(
                app(EnumValue::class)
                    ->getModelId($data['status'], 'key', ['type' =>'installation_status'])
            );
        }
    }
}
