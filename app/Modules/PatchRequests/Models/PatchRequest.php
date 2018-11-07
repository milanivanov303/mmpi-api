<?php

namespace App\Modules\PatchRequests\Models;

use App\Models\Model;
use App\Modules\Issues\Models\Issue;
use App\Modules\Modifications\Models\Modification;
use App\Modules\DeliveryChains\Models\DeliveryChain;
use App\Modules\Patches\Models\Patch;

class PatchRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'migr_src_email_request',
        'migr_src_email_request_files',
        'when_to_install_string',
        'when_to_install_datetime',
        'call_back_tech_valid',
        'notes',
        'comm_status',
        'greenlight_status',
        'greenlighted_on',
        'greenlighted_by',
        'customer_infomed',
        'nr_test',
        'automated_test',
        'assign_to_planned_ba'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'issue_id',
        'delivery_chain_id',
        'migrated_id'
    ];

    /**
     * Get issue
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get attached modifications
     */
    public function modifications()
    {
        return $this->belongsToMany(Modification::class, 'modif_to_pr', 'pr_id', 'modif_id')
                    ->wherePivot('removed', null)
                    ->orderBy('order');
    }

    /**
     * Get patch
     */
    public function patches()
    {
        return $this->hasMany(Patch::class);
    }

    /**
     * Get attached modifications
     */
    public function deliveryChain()
    {
        return $this->belongsTo(DeliveryChain::class);
    }
}
