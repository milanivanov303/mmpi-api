<?php

namespace Modules\SourceDependencies\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\SourceRevisions\Models\SourceRevision;

class SourceDependency extends Model
{
    /**
     * Set the table associated with the model.
     *
     * @var array
     */
    protected $table = "source_dependencies";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rev_id',
        'dep_id',
        'functional',
        'type',
        'table_name',
        'comment',
        'added_by',
        'added_on',
        'deleted',
        'scope'
    ];

    /**
     * Get user
     */
    protected function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get revision whose dependency this is
     */
    protected function revId()
    {
        return $this->belongsTo(SourceRevision::class, 'rev_id');
    }

    /**
     * Get revision of the dependency
     */
    protected function depId()
    {
        return $this->belongsTo(SourceRevision::class, 'dep_id');
    }
}
