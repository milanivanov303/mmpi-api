<?php

namespace Modules\ProjectEventEstimations\Repositories;

use App\Models\Department;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\ProjectEventEstimations\Models\ProjectEventEstimation;
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

    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('project_event', $data)) {
            $this->model->project()->associate(
                app(ProjectEvent::class)->getModelId($data['project_event'], 'id')
            );
        }

        if (array_key_exists('department', $data)) {
            $this->model->project()->associate(
                app(Department::class)->getModelId($data['department'], 'id')
            );
        }
    }
}
