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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'repoType',
        'branch',
        'files',
        'committedBy'
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
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'repo_type_id',
        'branch_id'
    ];

    /**
     * Get user for the hash.
     */
    public function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id');
    }

    /**
     * Get user for the hash.
     */
    public function branch()
    {
        return $this->belongsTo(HashBranch::class, 'branch_id');
    }

    /**
     * Get the files for the hash.
     */
    public function files()
    {
        return $this->hasMany(HashCommitFile::class, 'hash_commit_id');
    }

    /**
     * Get user for the hash.
     */
    public function committedBy()
    {
        return $this->belongsTo(User::class, 'committed_by');
    }
}
