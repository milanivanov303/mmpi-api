<?php

namespace Modules\Patches\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\Projects\Models\Project;
use Modules\DeliveryChains\Models\DeliveryChain;
use Modules\Installations\Models\Installation;

class Patch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
    protected function patchRequest()
    {
        return $this->belongsTo(PatchRequest::class);
    }

    /**
     * Get project
     */
    protected function patchGroup()
    {
        return $this->belongsTo(PatchGroup::class);
    }

    /**
     * Get project
     */
    protected function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get delivery chain
     */
    protected function deliveryChain()
    {
        return $this->belongsTo(DeliveryChain::class);
    }

    /**
     * Get checked by
     */
    protected function checkedBy()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get verified by
     */
    protected function verifiedBy()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get installations
     */
    protected function installations()
    {
        return $this->hasMany(Installation::class);
    }
}
