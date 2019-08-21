<?php

namespace Modules\SourceRevisions\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\Sources\Models\Source;

class SourceRevision extends Model
{
    /**
     * Set the table associated with the model.
     *
     * @var array
     */
    protected $table = "source_revision";

    /**
     * Set primary key
     *
     */
    protected $primaryKey = "rev_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_id',
        'rev_cksum',
        'revision',
        'revision_converted',
        'cvs_date',
        'cvs_editor',
        'cvs_lines',
        'source_revision_status',
        'rev_registration_date',
        'cvs_comment',
        'cvs_separated_comment',
        'buggy',
        'buggy_comment',
        'buggy_on',
        'buggy_by',
        'dep_log',
        'dep_warn',
        'creator',
        'validate_on',
        'validate_by',
        'requested_head_merge'
    ];

    /**
     * Get person who set the buggy flag on
     */
    protected function buggyBy()
    {
        return $this->belongsTo(User::class, 'buggy_by');
    }

    /**
     * Get revision whose dependency this is
     */
    protected function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    /**
     * Get user
     */
    protected function validateBy()
    {
        return $this->belongsTo(User::class, 'validate_by');
    }
}
