<?php

namespace Modules\SourceDependencies\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\SourceDependencies\Models\SourceDependency;

class SourceDependencyRepository extends AbstractRepository implements RepositoryInterface
{

    /**
     * SourceRepository constructor
     *
     * @param SourceDependency $model
     */
    public function __construct(SourceDependency $model)
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

        $this->model->addedBy()->associate(Auth::user());

        $this->model->added_on = Carbon::now()->format('Y-m-d H:i:s');
    }
}
