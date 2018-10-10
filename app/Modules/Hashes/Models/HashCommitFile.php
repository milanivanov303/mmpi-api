<?php

namespace App\Modules\Hashes\Models;

use App\Models\Model;

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
