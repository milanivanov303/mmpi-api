<?php

namespace Modules\ProjectEventEstimations\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\ProjectEventEstimations\Models\ProjectEventEstimation;
use Modules\Departments\Models\Department;
use Modules\ProjectEvents\Models\ProjectEvent;

class ProjectEventEstimationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectEventEstimationRepository constructor
     *
     * @param ProjectEventEstimation $model
     */
    public function __construct(ProjectEventEstimation $model)
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
