<?php

namespace Modules\PatchRequests\Models;

use Core\Models\Model;
use Modules\Issues\Models\Issue;
use Modules\Modifications\Models\Modification;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Patches\Models\Patch;
use App\Models\User;
use Modules\PatchRequests\Models\PatchRequestsSpecification;
use Modules\PatchRequests\Models\CurrentPrStatus;
use Modules\PatchRequests\Models\PatchRequestsStatusHistory;

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
        'assign_to_planned_ba',
        'issue_id',
        'delivery_chain_id',
        'migrated_id'
    ];

    /**
     * Get issue
     */
    protected function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get attached modifications
     */
    protected function modifications()
    {
        return $this->belongsToMany(Modification::class, 'modif_to_pr', 'pr_id', 'modif_id')
                    ->wherePivot('removed', null)
                    ->orderBy('order');
    }

    /**
     * Get removed modifications
     */
    protected function removedModifications()
    {
        return $this->belongsToMany(Modification::class, 'modif_to_pr', 'pr_id', 'modif_id')
            ->wherePivot('removed', "=", 1)
            ->orderBy('order');
    }

    /**
     * Get patch
     */
    protected function patches()
    {
        return $this->hasMany(Patch::class);
    }

    /**
     * Get attached modifications
     */
    protected function deliveryChain()
    {
        return $this->belongsTo(DeliveryChain::class);
    }

    /**
     * Get greenlighted by
     */
    protected function greenlightedBy()
    {
        return $this->belongsTo(User::class, 'greenlighted_by');
    }

    /**
     * Get attached specifications
     */
    protected function specifications()
    {
        return $this->hasMany(PatchRequestsSpecification::class);
    }

    /**
     * Get current status
     */
    protected function currentStatus()
    {
        return $this->hasOne(CurrentPrStatus::class, 'pr_id', 'id');
    }

    /**
     * Get records from table patch_requests_status_history
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function statusHistory()
    {
        return $this->hasMany(PatchRequestsStatusHistory::class);
    }
}
