<?php

namespace Modules\Sources\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\SourceRevisions\Models\SourceRevision;

class Source extends Model
{
    /**
     * Set the table associated with the model.
     *
     * @var array
     */
    protected $table = "source";

    /**
     * Set primary key
     *
     */
    protected $primaryKey = "source_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_name',
        'source_path',
        'source_status',
        'comment',
        'source_registration_date',
        'department_id',
        'department_assigned_by_id',
        'department_assigned_on',
        'dependencies',
        'library'
    ];

    /**
     * Get user
     */
    protected function departmentAssignedBy()
    {
        return $this->belongsTo(User::class, 'department_assigned_by_id');
    }

     /**
     * Get revisions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function revisions()
    {
        return $this->hasMany(SourceRevision::class, 'source_id');
    }
}
