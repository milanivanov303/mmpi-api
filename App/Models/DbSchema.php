<?php

namespace App\Models;

use Core\Models\Model;
use Modules\projects\Models\Project;

class DbSchema extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'project_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'project_id'
    ];

    /**
     * Get projects
     */
    protected function project()
    {
        return $this->belongsTo(Project::class);
    }
}
