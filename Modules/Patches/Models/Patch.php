<?php

namespace Modules\Patches\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\Projects\Models\Project;
use Modules\DeliveryChains\Models\DeliveryChain;

class Patch extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'project',
        'deliveryChain',
        'patchGroup',
        'checkedBy',
        'verifiedBy'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'patch_request_id',
        'delivery_chain_id',
        'project_id',
        'patch_group_id',
        'checked_by_id',
        'verified_by_id'
    ];

    /**
     * Get patch request
     */
    public function patchRequest()
    {
        return $this->belongsTo(PatchRequest::class);
    }

    /**
     * Get project
     */
    public function patchGroup()
    {
        return $this->belongsTo(PatchGroup::class);
    }

    /**
     * Get project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get delivery chain
     */
    public function deliveryChain()
    {
        return $this->belongsTo(DeliveryChain::class);
    }

    /**
     * Get checked by
     */
    public function checkedBy()
    {
        return $this->belongsTo(User::class)->minimal();
    }

    /**
     * Get verified by
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class)->minimal();
    }
}
