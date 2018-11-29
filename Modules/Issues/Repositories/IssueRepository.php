<?php

namespace Modules\Issues\Repositories;

use Modules\Core\Repositories\RepositoryInterface;
use Modules\Core\Repositories\AbstractRepository;
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
     * Save issue
     *
     * @param array $data
     * @return Issue
     *
     * @throws \Throwable
     */
    protected function save($data)
    {
        $this->model->fill($data);

        $this->model->project()->associate($data['project']['id']);
        $this->model->devInstance()->associate(
            isset($data['dev_instance']) ? $data['dev_instance']['id'] : null
        );

        $this->model->saveOrFail();

        $this->model->load($this->getWith());

        return $this->model;
    }
}
