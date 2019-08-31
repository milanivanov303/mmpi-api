<?php

namespace Modules\SourceRevisions\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\SourceRevisions\Models\SourceRevision;

class SourceRevisionRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Set primary key
     *
     */
    protected $primaryKey = "rev_id";

    /**
     * SourceRepository constructor
     *
     * @param SourceRevision $model
     */
    public function __construct(SourceRevision $model)
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

    /**
     * @inheritDoc
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        $this->model->buggyBy()->associate(Auth::user());

        $this->model->validateBy()->associate(Auth::user());

        $this->model->validate_on = Carbon::now()->format('Y-m-d H:i:s');

        $this->model->buggy_on = Carbon::now()->format('Y-m-d H:i:s');

        $this->model->rev_registration_date = Carbon::now()->format('Y-m-d H:i:s');
    }
}
