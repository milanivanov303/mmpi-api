<?php

namespace Modules\Departments\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Departments\Models\Department;
use App\Models\EnumValue;

class DepartmentRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * DepartmentRepository constructor
     *
     * @param Department $model
     */
    public function __construct(Department $model)
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
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('department_type', $data)) {
            $this->model->departmentType()->associate(
                app(EnumValue::class)->getModelId($data['department_type'], 'key', ['type' => 'department_type'])
            );
        }
    }
}
