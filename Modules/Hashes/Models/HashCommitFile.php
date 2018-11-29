<?php

namespace Modules\Hashes\Models;

use Modules\Core\Models\Model;

class HashCommitFile extends Model
{

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash_commit_id',
        'file_name'
    ];
}
