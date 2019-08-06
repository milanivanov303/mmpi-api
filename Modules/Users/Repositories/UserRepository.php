<?php

namespace Modules\Users\Repositories;

use Core\Repositories\AbstractRepository;
use Core\Repositories\RepositoryInterface;
use App\Models\User;

class UserRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Custom unique primaryKey
     *
     * @var string
     */
    protected $customUniqueKey = 'username';

    /**
     * UserRepository constructor
     *
     * @param User $model
     */
    public function __construct(User $model)
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
            'department' => function ($model, $value) {
                return $model->whereHas('department', function ($query) use ($value) {
                    $query->where('name', 'like', "%{$value}%");
                });
            },
            'manager' => function ($model, $value, $operator) {
                return $model->whereHas('manager', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'deputy' => function ($model, $value, $operator) {
                return $model->whereHas('deputy', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'access_group' => function ($model, $value, $operator) {
                return $model->whereHas('accessGroup', function ($query) use ($value, $operator) {
                    $query->where('name', $operator, $value);
                });
            }
        ];
    }

    /**
     * Define order by for this model
     *
     * @return array
     */
    public function orderBy(): array
    {
        return [
            'department_id' => function ($model, $order_dir) {
                return $model->select('users.*')->join('departments', 'departments.id', '=', 'users.department_id')
                    ->orderBy('departments.name', $order_dir);
            }
        ];
    }
}
