<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserRole;
use Core\Models\Model;
use Modules\Projects\Models\Project;

class UserProjectRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_prjs_roles';

    /**
     * Get user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get project
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * Get madeBy
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
