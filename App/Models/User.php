<?php

namespace App\Models;

use Core\Contracts\Models\User as UserContact;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;
use App\Models\Department;
use Modules\Auth\Models\Permission;

class User extends Model implements AuthenticatableContract, AuthorizableContract, UserContact
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
     * @inheritDoc
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public static function getByUsername(string $username, int $status = null) : ?self
    {
        $query = self::where('username', $username);

        if (is_null($status)) {
            return $query->first();
        }

        return $query->where('status', $status)->first();
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
        return $this->hasMany(UserProjectRole::class);
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

     /**
     * Get user custom and group permissions
     */
    public function permissions()
    {
        return Permission
            ::select(['permission_types.id as actions'])
            ->join('users_to_permissions as up', 'up.permission_type_id', '=', 'permission_types.id')
            ->join('group_permissions as gp', 'gp.permission_type_id', '=', 'permission_types.id')
            ->join('access_groups as ag', 'gp.group_id', '=', 'ag.id')
            ->where('up.user_id', $this->getId())
            ->orWhere('ag.id', $this->accessGroup->id)
            ->groupBy('actions')
            ->get();
    }
}
