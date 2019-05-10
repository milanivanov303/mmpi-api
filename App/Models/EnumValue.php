<?php

namespace App\Models;

use App\Models\User;

class EnumValue extends Model
{
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

    /**
     * Get specific enum value properties
     */
    public function scopeMinimal($query)
    {
        return $query->select(['id', 'type', 'subtype', 'key', 'value']);
    }
}
