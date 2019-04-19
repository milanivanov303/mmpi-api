<?php

namespace Modules\Hashes\Models;

use Core\Models\Model;

class HashCommitFile extends Model
{

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'hash_commit_id'
    ];
}
