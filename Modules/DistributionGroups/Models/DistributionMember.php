<?php

namespace Modules\DistributionGroups\Models;

use Core\Models\Model;

class DistributionMember extends Model
{

    /**
     * @var string
     */
    protected $table = 'distribution_members';

    /**
     * @var string[]
     */
    protected $fillable = [
        'distribution_groups_id',
        'username',
        'created_date',
        'status',
    ];

    /**
     * Relationship with Distribution Group
     */
    public function distributionGroup() {
        return $this->belongsTo(DistributionGroup::class, 'distribution_groups_id');
    }

}