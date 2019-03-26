<?php

namespace Modules\Modifications\Repositories;

use App\Models\EnumValue;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\Issues\Models\Issue;
use Modules\Modifications\Models\Modification;

class ModificationRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ProjectRepository constructor
     *
     * @param Modification $model
     */
    public function __construct(Modification $model)
    {
        $this->model = $model;
    }

    /**
     * Fill model attributes
     *
     * @param array $data
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('action_type', $data)) {
            $this->model->actionType()->associate(
                app(EnumValue::class)
                    ->getModelId($data['action_type'], 'key', ['type' => 'src_action_type'])
            );
        }

        if (array_key_exists('instance_status', $data)) {
            $this->model->instanceStatus()->associate(
                app(EnumValue::class)
                    ->getModelId($data['instance_status'], 'key', ['type' => 'instance_status'])
            );
        }

        if (array_key_exists('issue', $data)) {
            $this->model->issue()->associate(
                app(Issue::class)
                    ->getModelId($data['issue'], ['tts_id', 'jiraissue_id'])
            );
        }

        if (array_key_exists('path', $data)) {
            $this->model->path()->associate(
                app(EnumValue::class)
                    ->getModelId($data['path'], 'key', ['type' => 'source_paths'])
            );
        }
    }
}
