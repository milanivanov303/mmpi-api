<?php

namespace App\Modules\Issues\Repositories;

use App\Repositories\RepositoryInterface;
use App\Repositories\AbstractRepository;
use App\Modules\Issues\Models\Issue;
use App\Modules\Projects\Models\Project;
use App\Modules\Instances\Models\Instance;

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
     * Create new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->save($data);
    }

    /**
     * Update existing record
     *
     * @param array $data
     * @param mixed $id
     * @return Model
     */
    public function update(array $data, $id)
    {
        $this->model = $this->find($id);
        return $this->save($data);
    }

    /**
     * Save issue
     *
     * @param array $data
     * @return Model
     */
    protected function save($data)
    {
        $this->model->fill($data);

        $this->associateProject($data['project']);
        $this->associateDevInstance($data['dev_instance']);

        $this->model->saveOrFail();

        $this->model->load($this->getWith());

        return $this->model;
    }

    /**
     * Associate project
     *
     * @param array $data Project data
     */
    protected function associateProject($data)
    {
        $project = new Project();
        $project->id = $data['id'];
        $this->model->project()->associate($project);
    }

    /**
     * Associate dev instance
     *
     * @param array $data Instance data
     */
    protected function associateDevInstance($data)
    {
        $instance = new Instance();
        $instance->id = $data['id'];
        $this->model->devInstance()->associate($instance);
    }
}
