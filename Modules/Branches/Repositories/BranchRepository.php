<?php

namespace Modules\Branches\Repositories;

use Carbon\Carbon;
use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\Branches\Models\Branch;
use App\Models\EnumValue;

class BranchRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * BranchRepository constructor
     *
     * @param Branch $model
     */
    public function __construct(Branch $model)
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
        return [
            'repo_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('repoType', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            }
        ];
    }

    /**
     * @inheritDoc
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('repo_type', $data)) {
            $this->model->repoType()->associate(
                app(EnumValue::class)->getModelId($data['repo_type'], 'key', ['type' => 'repository_type'])
            );
        }

        $this->model->madeBy()->associate(Auth::user());

        $this->model->created_at = Carbon::now()->format('Y-m-d H:i:s');
    }
}
