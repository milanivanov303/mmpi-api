<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Modules\Projects\Models\Project;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

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
     * Get id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
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
     * Get user by sid
     *
     * @param string $sid
     * @return User|null
     */
    public static function getBySid(string $sid) : ?self
    {
        return self::where('sidfr', $sid)->first();
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
    protected function accessGroup()
    {
        return $this->belongsTo(AccessGroup::class);
    }

    /**
     * Get user manager.
     */
    protected function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get user deputy.
     */
    protected function deputy()
    {
        return $this->belongsTo(User::class, 'deputy_id');
    }

    /**
     * Get user roles.
     */
    protected function roles()
    {
        return $this->belongsToMany(Project::class, 'users_prjs_roles')
        ->withPivot('role_id');
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
