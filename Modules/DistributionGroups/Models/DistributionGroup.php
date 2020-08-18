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

    public function members() {
        \Log::error('test');
        return $this->hasMany(DistributionMember::class, 'distribution_groups_id', 'distribution_groups_id');
    }

}