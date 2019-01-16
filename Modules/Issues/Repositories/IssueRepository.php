<?php

namespace Modules\Issues\Repositories;

use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Modules\Issues\Models\Issue;

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

        $this->model->project()->associate($data['project']['id']);

        $this->model->devInstance()->associate(
            isset($data['dev_instance']) ? $data['dev_instance']['id'] : null
        );

        $this->model->parentIssue()->associate(
            isset($data['parent_issue']) ? $data['parent_issue']['id'] : null
        );
    }
}
