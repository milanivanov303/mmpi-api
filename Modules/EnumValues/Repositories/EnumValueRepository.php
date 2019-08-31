<?php

namespace Modules\EnumValues\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use App\Models\EnumValue;
use Illuminate\Support\Facades\Auth;

class EnumValueRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Custom unique primaryKey
     *
     * @var array
     */
    protected $customUniqueKey = ['type', 'key'];

    /**
     * EnumValueRepository constructor
     *
     * @param EnumValue $model
     */
    public function __construct(EnumValue $model)
    {
        $this->model = $model;
    }

    protected function fillModel(array $data)
    {
        parent::fillModel($data);
        
        $this->model->changedBy()->associate(Auth::user());

        $this->model->changed_on = Carbon::now()->format('Y-m-d H:i:s');
    }
}
