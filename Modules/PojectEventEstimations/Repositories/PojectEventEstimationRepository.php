<?php

namespace Modules\PojectEventEstimations\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\PojectEventEstimations\Models\PojectEventEstimation;
use Modules\Departments\Models\Department;
use Modules\ProjectEvents\Models\ProjectEvent;

class PojectEventEstimationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * PojectEventEstimationRepository constructor
     *
     * @param PojectEventEstimation $model
     */
    public function __construct(PojectEventEstimation $model)
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

        if (array_key_exists('project_event', $data)) {
            $this->model->projectEvent()->associate(
                app(ProjectEvent::class)->getModelId($data['project_event'], 'id')
            );
        }

        if (array_key_exists('department', $data)) {
            $this->model->department()->associate(
                app(Department::class)->getModelId($data['department'], 'id')
            );
        }
    }
}
