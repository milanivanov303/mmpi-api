<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Traits\Mappable;
use App\Traits\Filterable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    use Mappable;
    use Filterable;

    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [
        'manager_id'      => 'manager',
        'deputy_id'       => 'deputy',
        'department_id'   => 'department',
        'access_group_id' => 'access_group'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password'
    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    protected function filters(): array
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
    protected function orderBy(): array
    {
        return [
            'department_id' => function ($model, $order_dir) {
                return $model->join('departments', 'departments.id', '=', 'users.department_id')->orderBy('departments.name', $order_dir);
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
        return $this->belongsTo('App\Models\Department');
    }
    
    /**
     * Get user access group.
     */
    public function accessGroup()
    {
        return $this->belongsTo('App\Models\AccessGroup');
    }

    /**
     * Get user manager.
     */
    public function manager()
    {
        return $this->belongsTo('App\Models\User', 'manager_id', 'id');
    }

    /**
     * Get user deputy.
     */
    public function deputy()
    {
        return $this->belongsTo('App\Models\User', 'deputy_id', 'id');
    }

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray()
    {
        $array = parent::relationsToArray();

        if ($this->isVisible('department_id') && array_key_exists('department', $array)) {
            $array['department_id'] = $array['department']['name'];
        }

        if ($this->isVisible('access_group_id') && array_key_exists('accessGroup', $array)) {
            $array['access_group_id'] = $array['accessGroup']['name'];
        }

        if ($this->isVisible('manager_id') && array_key_exists('manager', $array)) {
            $array['manager_id'] = $array['manager']['username'];
        }

        if ($this->isVisible('deputy_id') && array_key_exists('deputy', $array)) {
            $array['deputy_id'] = $array['deputy']['username'];
        }

        return $array;
    }
}
