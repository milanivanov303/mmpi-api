<?php

namespace Modules\Modifications\Models;

class SOAdeploymentModification extends Modification
{
    protected static $type = 'soa';

    protected $visible = [
        'id',
        'name',
        'type',
        'issue',
        'deliveryChain',
        'instanceStatus',
        'deployment_path'
    ];
}
