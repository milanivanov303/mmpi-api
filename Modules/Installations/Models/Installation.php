<?php

namespace Modules\Installations\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use App\Models\User;
use Modules\Instances\Models\Instance;
use Modules\Patches\Models\Patch;

class Installation extends Model
{
    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'patch_id',
        'instance_id',
        'status_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patch_id',
        'instance_id',
        'installed_on',
        'status_id',
        'err_output',
        'duration',
        'log_file',
        'timezone_converted'
    ];

    /**
     * Get instances
     */
    protected function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get patch
     */
    protected function patch()
    {
        return $this->belongsTo(Patch::class);
    }

    /**
     * Get status
     */
    protected function status()
    {
        return $this->belongsTo(EnumValue::class, 'status_id');
    }
}
