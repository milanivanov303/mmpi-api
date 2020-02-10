<?php

namespace Modules\Modifications\Models;

class BinaryModification extends Modification
{
    protected static $type = 'binary';

    protected $visible = [
        'id',
        'issue',
        'issue_id',
        'type',
        'type_id',
        'subtype',
        'subtype_id',
        'version',
        'revision_converted',
        'name',
        'maven_repository',
        'deployment_prefix_id',
        'deploymentPrefix',
        'delivery_chain_id',
        'deliveryChain',
        'instanceStatus',
        'active',
        'visible'
    ];
}
