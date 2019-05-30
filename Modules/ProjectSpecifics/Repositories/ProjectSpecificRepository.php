<?php

namespace Modules\ProjectSpecifics\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\ProjectSpecifics\Models\ProjectSpecific;
use Modules\Projects\Models\Project;
use App\Models\EnumValue;

class ProjectSpecificRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectSpecificRepository constructor
     *
     * @param ProjectSpecific $model
     */
    public function __construct(ProjectSpecific $model)
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

    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('project', $data)) {
            $this->model->project()->associate(
                app(Project::class)
                    ->getModelId($data['project'], 'name')
            );
        }

        $this->model->madeBy()->associate(Auth::user());

        $this->model->made_on = Carbon::now()->format('Y-m-d H:i:s');

        if (array_key_exists('project_specific_feature', $data)) {
            $this->model->projectSpecificFeature()->associate(
                app(EnumValue::class)
                    ->getModelId($data['project_specific_feature'], 'key', ['type' => 'project_specific_feature'])
            );
        }
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
        $project = Project::find($model->project->id);
        $project->projectSpecifics()->get()->forget($id);

        return $model->delete();
    }
}
