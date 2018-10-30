<?php

namespace App\Modules\PatchRequests\Models;

use App\Models\Model;
use App\Models\Project;

class Patch extends Model
{
    /**
     * Get issue project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
