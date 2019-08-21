<?php

namespace Modules\Sources\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\Sources\Models\Source;

class SourceRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * SourceRepository constructor
     *
     * @param Source $model
     */
    public function __construct(Source $model)
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
            'department_assigned_by' => function ($builder, $value, $operator) {
                return $builder->whereHas('departmentAssignedBy', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            }
        ];
    }

    /**
     * @inheritDoc
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        $this->model->departmentAssignedBy()->associate(Auth::user());

        $this->model->source_registration_date = Carbon::now()->format('Y-m-d H:i:s');
    }
}
