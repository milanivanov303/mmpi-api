<?php

namespace App\Modules\Issues\Repositories;

use App\Repositories\AbstractEloquentRepository;
use Illuminate\Support\Facades\DB;

class EloquentIssueRepository extends AbstractEloquentRepository implements IssueRepository
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
}
