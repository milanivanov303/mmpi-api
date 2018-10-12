<?php

namespace App\Modules\Hashes\Models;

use App\Models\Model;
use App\Models\User;
use App\Traits\Mappable;
use App\Traits\Filterable;

class HashCommit extends Model
{
    use Mappable;
    use Filterable;

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
     * Define filters for this model
     *
     * @return array
     */
    protected function filters(): array
    {
        return [
            'committed_by' => function ($model, $value) {
                return $model->whereHas('owner', function ($query) use ($value) {
                    $query->where('username', '=', $value);
                });
            },
            'files' => function ($model, $value) {
                return $model->whereHas('files', function ($query) use ($value) {
                    $query->where('file_name', 'like', "%{$value}%");
                });
            },
            'chains' => function ($model, $value) {
                return $model->whereHas('chains', function ($query) use ($value) {
                    $query->whereHas('chain', function ($query) use ($value) {
                        $query->where('chain_name', 'like', "%{$value}%");
                    });
                });
            }
        ];
    }

    /**
     * Define order by for this model
     *
     * @return array
     */
    protected function orderBy(): array
    {
        return [

        ];
    }

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'id',
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
        return $this->hasMany(HashCommitToChain::class, 'hash_commit_id');
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
        $user = User::where('username', $value)->firstOrFail();
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
        $array['files'] = array_column($this->files->toArray(), 'file_name');
        
        // convert chains relations to simple array with names
        $array['chains'] = array_column(
            array_column($this->chains->toArray(), 'chain'),
            'chain_name'
        );

        // set commited by to owner username
        $array['committed_by'] = $this->owner['username'];
        
        return $array;
    }
}
