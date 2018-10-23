<?php

namespace App\Modules\Issues\Repositories;

use App\Repositories\RepositoryInterface;
use App\Repositories\AbstractRepository;
use App\Modules\Issues\Models\Issue;

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
        'project',
        'devInstance',
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
}
