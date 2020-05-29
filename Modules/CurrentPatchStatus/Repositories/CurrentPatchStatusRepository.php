<?php

namespace Modules\CurrentPatchStatus\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Modules\CurrentPatchStatus\Models\CurrentPatchStatus;

class CurrentPatchStatusRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * CurrentPatchStatusRepository constructor
     *
     * @param CurrentPatchStatus $model
     */
    public function __construct(CurrentPatchStatus $model)
    {
        $this->model = $model;
    }

    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        $this->model->date = Carbon::now()->format('Y-m-d H:i:s');
    }
}
