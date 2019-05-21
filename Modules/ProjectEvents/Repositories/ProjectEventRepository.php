<?php

namespace Modules\ProjectEvents\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\ProjectEvents\Models\ProjectEvent;
use Modules\Projects\Models\Project;
use App\Models\EnumValue;

class ProjectEventRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectEventRepository constructor
     *
     * @param ProjectEvent $model
     */
    public function __construct(ProjectEvent $model)
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
            'project' => function ($builder, $value, $operator) {
                return $builder->whereHas('project', function ($query) use ($value, $operator) {
                    $query->where('name', $operator, $value);
                });
            },
            'project_event_status' => function ($builder, $value, $operator) {
                return $builder->whereHas('projectEventStatus', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'project_event_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('projectEventType', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            }
        ];
    }

    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('project', $data)) {
            $this->model->project()->associate(
                app(Project::class)->getModelId($data['project'], 'name')
            );
        }

        $this->model->madeBy()->associate(Auth::user());

        $this->model->made_on = Carbon::now()->format('Y-m-d H:i:s');

        if (array_key_exists('project_event_type', $data)) {
            $this->model->projectEventType()->associate(
                app(EnumValue::class)
                    ->getModelId($data['project_event_type'], 'key', ['type' =>'project_event_type'])
            );
        }

        if (array_key_exists('project_event_status', $data)) {
            $this->model->projectEventStatus()->associate(
                app(EnumValue::class)
                    ->getModelId($data['project_event_status'], 'key', ['type' =>'project_event_status'])
            );
        }
    }
}
