<?php

namespace Modules\InstanceDowntimes\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\Instances\Models\Instance;

class InstanceDowntime extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_datetime',
        'end_datetime',
        'status',
        'description',
        'made_by',
        'instance_id'
    ];

    /**
     * Get instances
     */
    protected function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get users
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
