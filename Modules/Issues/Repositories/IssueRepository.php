<?php

namespace Modules\Issues\Repositories;

use App\Models\Model;
use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Modules\Instances\Models\Instance;
use Modules\Issues\Models\Issue;
use Modules\Projects\Models\Project;

class IssueRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'tts_id';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'parentIssue'
    ];

    /**
     * HashRepository constructor
     *
     * @param Issue $model
     */
    public function __construct(Issue $model)
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

        if (array_key_exists('project', $data)) {
            $this->model->project()->associate(
                app(Project::class)->getModelId($data['project'], 'name')
            );
        }

        if (array_key_exists('dev_instance', $data)) {
            $this->model->devInstance()->associate(
                app(Instance::class)->getModelId($data['dev_instance'], 'name')
            );
        }

        if (array_key_exists('parent_issue', $data)) {
            $this->model->parentIssue()->associate(
                app(Issue::class)->getModelId($data['parent_issue'], 'tts_id')
            );
        }
    }
}
