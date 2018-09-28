<?php

namespace App\Models\Hashes;

use App\Models\Model;

class HashCommitToChain extends Model
{

    
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'chain'
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash_chain_id'
    ];
    
    /**
     * Get the chain.
     */
    public function chain()
    {
        return $this->hasOne('App\Models\Hashes\HashChain', 'id', 'hash_chain_id');
    }
}
