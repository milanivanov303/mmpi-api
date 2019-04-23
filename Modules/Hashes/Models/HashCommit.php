<?php

namespace Modules\Hashes\Models;

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
        'repo_branch'        => 'branch',
        'commit_description' => 'description',
        'repo_merge_branch'  => 'merge_branch',
        'repo_module'        => 'module',
        'committed_by'       => 'owner',
        'hash_rev'           => 'rev'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'repo_branch',
        'commit_description',
        'repo_merge_branch',
        'repo_module',
        'committed_by',
        'repo_path',
        'repo_url',
        'hash_rev',
        'repo_timestamp'
    ];

    /**
     * Get the files for the hash.
     */
    public function files()
    {
        return $this->hasMany(HashCommitFile::class, 'hash_commit_id');
    }

    /**
     * Get the files for the hash.
     */
    public function chains()
    {
        return $this->belongsToMany(HashChain::class, 'hash_commit_to_chains');
    }

    /**
     * Get user for the hash.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'committed_by');
    }

    /**
     * Set committed by attribute
     *
     * @param string $value
     */
    public function setCommittedByAttribute($value)
    {
        $user = User::where('username', $value)->first();
        $this->attributes['committed_by'] = $user->id;
    }

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray()
    {
        $array = parent::relationsToArray();

        // convert files relations to simple array with names
        if ($this->isVisible('files') && array_key_exists('files', $array)) {
            $array['files'] = array_column($array['files'], 'file_name');
        }

        // convert chains relations to simple array with names
        if ($this->isVisible('chains') && array_key_exists('chains', $array)) {
            $array['chains'] = array_column($array['chains'], 'chain_name');
        }

        // set committed_by to owner username
        if ($this->isVisible('committed_by') && array_key_exists('owner', $array)) {
            $array['committed_by'] = $array['owner']['username'];
        }

        return $array;
    }
}
