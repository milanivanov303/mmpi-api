<?php

namespace Modules\Hashes\Models;

use App\Models\EnumValue;
use Core\Models\Model;
use App\Models\User;

class HashCommit extends Model
{
    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [
        'commit_description' => 'description'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'repo_type_id',
        'branch_id',
        'committed_by'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rev',
        'hash_rev',
        'committed_by',
        'merge_branch',
        'repo_timestamp',
        'version',
        'commit_description',
        'made_on'
    ];

    /**
     * Get user for the hash.
     */
    protected function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id');
    }

    /**
     * Get user for the hash.
     */
    protected function branch()
    {
        return $this->belongsTo(HashBranch::class, 'branch_id');
    }

    /**
     * Get the files for the hash.
     */
    protected function files()
    {
        return $this->hasMany(HashCommitFile::class, 'hash_commit_id');
    }

    /**
     * Get user for the hash.
     */
    protected function committedBy()
    {
        return $this->belongsTo(User::class, 'committed_by');
    }
}
