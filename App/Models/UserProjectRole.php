<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserRole;
use Core\Models\Model;
use Modules\Projects\Models\Project;

class UserProjectRole extends Model
{
    protected $primaryKey = '';
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_prjs_roles';

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
    protected function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get project
     */
    protected function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * Get madeBy
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
