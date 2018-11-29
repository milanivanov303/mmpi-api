<?php

namespace App\Models;

class SourceRevTtsKey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_rev_tag_id',
        'cvs_tag_tts_key',
        'sortindex',
        'issue_id'
    ];
}
