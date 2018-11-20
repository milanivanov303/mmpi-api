<?php

namespace App\Modules\Instances\Models;

use App\Models\Model;
use App\Models\User;
use App\Models\EnumValue;
use App\Modules\DeliveryChains\Models\DeliveryChainType;

class Instance extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'owner',
        'instanceType',
        'status',
        'environmentType'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'instance_type_id',
        'environment_type_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'play_as_demo',
        'owner',
        'status',
        'timezone',
        'host',
        'user',
        'db_user',
        'tns_name',
        'has_patch_install_in_init',
        'instance_type_id',
        'environment_type_id'
    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'owner' => function ($builder, $value, $operator) {
                return $builder->whereHas('owner', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'status' => function ($builder, $value, $operator) {
                return $builder->whereHas('status', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'environment_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('environmentType', function ($query) use ($value, $operator) {
                    $query->where('type', $operator, $value);
                });
            },
            'instance_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('instanceType', function ($query) use ($value, $operator) {
                    $query->where('id', $operator, $value);
                });
            },
        ];
    }

    /**
     * Define order by for this model
     *
     * @return array
     */
    public function orderBy(): array
    {
        return [

        ];
    }

    /**
     * Get owner
     */
    public function owner()
    {
        return $this->belongsTo(EnumValue::class, 'owner')->minimal();
    }

    /**
     * Get instance type
     */
    public function instanceType()
    {
        return $this->belongsTo(InstanceType::class);
    }

    /**
     * Get environment type
     */
    public function environmentType()
    {
        return $this->belongsTo(DeliveryChainType::class);
    }

    /**
     * Get status
     */
    public function status()
    {
        return $this->belongsTo(EnumValue::class, 'status')->minimal();
    }
}
