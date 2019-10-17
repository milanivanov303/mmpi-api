<?php

namespace Modules\Modifications\Models;

class SOAdeploymentModification extends Modification
{
    protected static $type = 'soa';

    protected $visible = [
        'id',
        'name',
        'type',
        'deliveryChain',
        'instanceStatus',
        'deployment_path'
    ];
}
