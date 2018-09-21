<?php

namespace App\Models\Hashes;

use App\Models\Model;

class HashCommit extends Model
{    
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
        return $this->hasMany('App\Models\Hashes\HashCommitFile', 'hash_commit_id');
    }
    
    /**
     * Get the files for the hash.
     */
    public function chains()
    {
        return $this->hasMany('App\Models\Hashes\HashCommitToChain', 'hash_commit_id');
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
        $array['files'] = array_column($array['files'], 'file_name');
        
        // convert chains relations to simple array with names
        $array['chains'] = array_column(
            array_column($array['chains'], 'chain'),
            'chain_name'
        );
        
        return $array;
    }
}
