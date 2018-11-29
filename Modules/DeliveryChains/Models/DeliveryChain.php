<?php

namespace Modules\DeliveryChains\Models;

use Modules\Core\Models\Model;
use App\Models\EnumValue;

class DeliveryChain extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'type',
        'dlvryType',
        'status',
        'dcVersion',
        'dcRole'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'type_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'patch_directory_name'
    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'type' => function ($builder, $value, $operator) {
                return $builder->whereHas('type', function ($query) use ($value, $operator) {
                    $query->where('type', $operator, $value);
                });
            },
            'status' => function ($builder, $value, $operator) {
                return $builder->whereHas('status', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'dlvry_type' => function ($builder, $value, $operator) {
                return $builder->whereHas('dlvryType', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'dc_version' => function ($builder, $value, $operator) {
                return $builder->whereHas('dcVersion', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            },
            'dc_role' => function ($builder, $value, $operator) {
                return $builder->whereHas('dcRole', function ($query) use ($value, $operator) {
                    $query->where('key', $operator, $value);
                });
            }
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
            'type' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                             ->join('delivery_chain_types', 'delivery_chain_types.id', '=', 'delivery_chains.type_id')
                             ->orderBy('delivery_chain_types.type', $order_dir);
            },
            'status' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                             ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.status')
                             ->orderBy('enum_values.key', $order_dir);
            },
            'dlvry_type' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                             ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.dlvry_type')
                             ->orderBy('enum_values.key', $order_dir);
            },
            'dc_version' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                             ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.dc_version')
                             ->orderBy('enum_values.key', $order_dir);
            },
            'dc_role' => function ($model, $order_dir) {
                return $model->select('delivery_chains.*')
                             ->join('enum_values', 'enum_values.id', '=', 'delivery_chains.dc_role')
                             ->orderBy('enum_values.key', $order_dir);
            },
        ];
    }

    /**
     * Get type
     */
    public function type()
    {
        return $this->belongsTo(DeliveryChainType::class, 'type_id');
    }

    /**
     * Get dlvry_type
     */
    public function dlvryType()
    {
        return $this->belongsTo(EnumValue::class, 'dlvry_type')->minimal();
    }

    /**
     * Get status
     */
    public function status()
    {
        return $this->belongsTo(EnumValue::class, 'status')->minimal();
    }

    /**
     * Get dc_version
     */
    public function dcVersion()
    {
        return $this->belongsTo(EnumValue::class, 'dc_version')->minimal();
    }

    /**
     * Get dc_role
     */
    public function dcRole()
    {
        return $this->belongsTo(EnumValue::class, 'dc_role')->minimal();
    }
}
