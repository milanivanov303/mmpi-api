<?php

namespace Modules\Modifications\Models;

class SeTransferModification extends Modification
{
    protected static $type = 'se';

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
        'visible'
    ];
}
