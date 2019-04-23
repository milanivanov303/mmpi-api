<?php

namespace App\Models;

use App\Models\User;

class EnumValue extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'changedBy'
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'subtype',
        'key',
        'value',
        'description',
        'url',
        'active',
        'sortindex',
        'extra_property',
        'changed_on',
        'changed_by'
    ];
    
    /**
     * Get changedBy
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
