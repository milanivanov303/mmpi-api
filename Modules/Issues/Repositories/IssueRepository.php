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

        if (array_key_exists('project', $data)) {
            $this->model->project()->associate($data['project']['id']);
        }

        if (array_key_exists('dev_instance', $data)) {
            $this->model->devInstance()->associate(
                is_null($data['dev_instance']) ? null : $data['dev_instance']['id']
            );
        }

        if (array_key_exists('dev_instance', $data)) {
            $this->model->parentIssue()->associate(
                is_null($data['parent_issue']) ? null : $data['parent_issue']['id']
            );
        }
    }
}
