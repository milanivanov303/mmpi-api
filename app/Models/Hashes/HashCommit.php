<?php

namespace App\Models\Hashes;

use App\Models\Model;
use App\Traits\Mappable;

class HashCommit extends Model
{
    use Mappable;
    
    /**
     * Array with mapped attributes for conversion
     * @var array
     */
    protected $mapping = [
        'repo_branch'        => 'branch',
        'commit_description' => 'description',
        'repo_merge_branch'  => 'merge_branch',
        'repo_module'        => 'module',
        'committed_by'       => 'owner',
        'repo_path'          => 'repo_path',
        'repo_url'           => 'repo_url',
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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'files',
        'chains'
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
        if (array_key_exists('files', $array)) {
            $array['files'] = array_column($array['files'], 'file_name');
        }
        
        // convert chains relations to simple array with names
        if (array_key_exists('chains', $array)) {
            $array['chains'] = array_column(
                array_column($array['chains'], 'chain'),
                'chain_name'
            );
        }
        
        return $array;
    }
}
