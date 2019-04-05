<?php

namespace Modules\Projects\Models;

use App\Models\User;
use Core\Models\Model;

class ProjectRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_prjs_roles';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'user'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id'
    ];

    /**
     * Get modifiedBy
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
