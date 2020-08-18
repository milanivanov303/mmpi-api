<?php

namespace Modules\DistributionGroups\Models;

use Core\Models\Model;

class DistributionGroup extends Model
{

    /**
     * @var string
     */
    protected $table = 'distribution_groups';

    /**
     * @var array
     */
    protected $fillable = [
        'samaccountname',
        'displayname',
        'distinguished_name',
        'created_date',
        'email',
    ];

    /**
     * Relation for Distribution Members
     */
    public function members() {
        return $this->hasMany(DistributionMember::class, 'distribution_groups_id', 'distribution_groups_id');
    }

}