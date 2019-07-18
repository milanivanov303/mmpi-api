<?php

namespace Modules\Hashes\Models;

use App\Models\EnumValue;
use App\Models\User;
use Core\Models\Model;

class HashBranch extends Model
{
    /**
     * Get user for the hash.
     */
    protected function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id');
    }

    /**
     * Get user for the hash.
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
