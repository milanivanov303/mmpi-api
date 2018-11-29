<?php

namespace App\Models;

class CommitMerge extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'commit_merge';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commit_log_type_id',
        'commit_id',
        'merge_commit'
    ];
}
