<?php

namespace Modules\Users\Models;

use Core\Models\Model;
use Modules\Projects\Models\Project;

class UserRole extends Model
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
        'project_id'
    ];

    /**
     * Get modifiedBy
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
