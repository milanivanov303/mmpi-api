<?php

namespace Modules\Branches\Models;

use Core\Models\Model;
use App\Models\EnumValue;
use App\Models\User;
use Modules\DeliveryChains\Models\DeliveryChain;

class Branch extends Model
{
    /**
     * Set the table associated with the model.
     *
     * @var array
     */
    protected $table = "hash_branches";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'repo_type_id',
        'description',
        'created_at',
        'made_by',
        'repo_master_branch',
        'status'
    ];

    /**
     * Get repository type
     */
    protected function repoType()
    {
        return $this->belongsTo(EnumValue::class, 'repo_type_id');
    }

    /**
     * Get users
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }

    /**
     * Get delivery chains.
     */
    protected function deliveryChains()
    {
        return $this->belongsToMany(DeliveryChain::class, 'delivery_chain_branch', 'repo_branch_id');

    }
}
