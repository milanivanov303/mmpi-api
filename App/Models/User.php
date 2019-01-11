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
     * Check if user is super admin
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return false;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return User|null
     */
    public static function getByEmail(string $email) : ?self
    {
        return self::where('email', $email)->active()->first();
    }

    /**
     * Get user by username
     *
     * @param string $username
     * @return User|null
     */
    public static function getByUsername(string $username) : ?self
    {
        return self::where('username', $username)->active()->first();
    }

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

    /**
     * Get only active users
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
