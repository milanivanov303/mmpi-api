<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'department',
        'accessGroup'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'manager_id',
        'deputy_id',
        'department_id',
        'access_group_id'
    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'department_id' => function ($model, $value) {
                return $model->whereHas('department', function ($query) use ($value) {
                    $query->where('name', 'like', "%{$value}%");
                });
            },
            'manager_id' => function ($model, $value, $operator) {
                return $model->whereHas('manager', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'deputy_id' => function ($model, $value, $operator) {
                return $model->whereHas('deputy', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'access_group_id' => function ($model, $value, $operator) {
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

    /**
     * Hash user password
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Auth user
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function auth($email, $password)
    {
        $user = self::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Get user department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get user access group.
     */
    public function accessGroup()
    {
        return $this->belongsTo(AccessGroup::class);
    }

    /**
     * Get user manager.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get user deputy.
     */
    public function deputy()
    {
        return $this->belongsTo(User::class, 'deputy_id');
    }
}
