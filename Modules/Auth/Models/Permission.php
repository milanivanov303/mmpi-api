<?php

namespace Modules\Auth\Models;

use App\Models\Model;

class Permission extends Model
{
    /**
     * Set the table associated with the model.
     *
     * @var array
     */
    protected $table = "permission_types";
}
