<?php

namespace App\Models;

use App\Models\User;
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
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'project_id'
    ];

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
     * Get madeBy
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
