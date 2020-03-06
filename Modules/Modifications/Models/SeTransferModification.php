<?php

namespace Modules\Modifications\Models;

use App\Models\Model;

class SeTransferModification extends Model
{
    protected static $type = 'se';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comments',
        'maven_repository',
        'active',
        'visible',
        'issue_id',
        'delivery_chain_id',
        'instance_id',
        'subtype_id',
        'created_by_id',
        'type_id',
        'created_on',
        'instance_status'
    ];

    protected $visible = [
        'id',
        'issue',
        'issue_id',
        'type',
        'type_id',
        'subtype',
        'subtype_id',
        'instance',
        'instance_id',
        'delivery_chain_id',
        'deliveryChain',
        'instanceStatus',
        'active',
        'comment',
        'visible'
    ];
}
