<?php

namespace Modules\InstanceDowntimes\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\Instances\Models\Instance;

class InstanceDowntime extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'instance',
        'madeBy'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'instance_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'instance_id',
        'start_datetime',
        'end_datetime',
        'made_by',
        'made_on',
        'status',
        'description'
    ];

    /**
     * Get instances
     */
    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get users
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by')->minimal();
    }
}
