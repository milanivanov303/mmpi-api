<?php

namespace Modules\Hashes\Models;

use App\Models\CommitMerge;
use App\Models\Dependency;
use App\Models\EnumValue;
use App\Models\SourceRevCvsTag;
use App\Models\SourceRevTtsKey;
use Core\Models\Model;
use App\Models\User;
use Modules\Branches\Models\Branch;

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
        'made_on',
        'repo_type_id',
        'branch_id',
        'requested_head_merge'
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
        return $this->belongsTo(Branch::class, 'branch_id');
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

    /**
     * Get dependencies
     */
    protected function dependencies()
    {
        return $this->hasManyThrough(
            Dependency::class,
            SourceRevCvsTag::class,
            'source_rev_id',
            'rev_id'
        );
    }

    /**
     * Get tts tickets
     */
    protected function ttsKeys()
    {
        return $this->hasManyThrough(
            SourceRevTtsKey::class,
            SourceRevCvsTag::class,
            'source_rev_id',
            'source_rev_tag_id'
        );
    }

    /**
     * Get merge
     */
    protected function merge()
    {
        return $this->hasOne(CommitMerge::class, 'commit_id');
    }
}
