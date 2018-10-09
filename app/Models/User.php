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
        'password',
        'department_id',
        'access_group_id',
        'manager_id',
        'deputy_id'
    ];

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
    public function access_group()
    {
        return $this->belongsTo('App\Models\AccessGroup');
    }

    /**
     * Get user manager.
     */
    public function manager()
    {
        return $this->belongsTo('App\Models\User', 'id', 'manager_id');
    }

    /**
     * Get user deputy.
     */
    public function deputy()
    {
        return $this->belongsTo('App\Models\User', 'id', 'deputy_id');
    }

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray()
    {
        $array = parent::relationsToArray();
        
        $array['department']   = $this->department['name'];
        $array['access_group'] = $this->access_group['name'];
        $array['manager'] = $this->manager['username'];
        $array['deputy'] = $this->deputy['username'];

        return $array;
    }

}
