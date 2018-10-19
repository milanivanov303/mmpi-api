<?php

namespace App\Modules\Hashes\Models;

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
        return $this->belongsTo(HashChain::class, 'hash_chain_id');
    }
}
