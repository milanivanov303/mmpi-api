<?php

namespace App\Models;

class EnumValue extends Model
{
    public function scopeMinimal($query)
    {
        return $query->select(['id', 'key', 'value']);
    }
}
