<?php

namespace App\Models;

class SourceRevCvsTag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_rev_id',
        'cvs_tag_enum_id',
        'cvs_tag_comment',
        'rev_log_type_id'
    ];
}
