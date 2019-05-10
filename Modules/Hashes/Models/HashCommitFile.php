<?php

namespace Modules\Hashes\Models;

use Core\Models\Model;

class HashCommitFile extends Model
{
    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [
        'file_name' => 'name'
    ];

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
        'id',
        'hash_commit_id'
    ];
}
