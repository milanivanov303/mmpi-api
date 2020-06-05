<?php

namespace App\Models;

use App\Models\User;
use Core\Models\Model;

class UserDepartmentRoles extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_depts_roles';

    /**
     * Get user
     */
    protected function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get project
     */
    protected function department()
    {
        return $this->belongsTo(Department::class);
    }
}
