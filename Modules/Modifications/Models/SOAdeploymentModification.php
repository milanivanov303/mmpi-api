<?php

namespace Modules\Modifications\Models;

class SOAdeploymentModification extends Modification
{
    protected static $type = 'soa';

    protected $visible = [
        'id',
        'name',
        'type',
        'type_id',
        'issue',
        'issue_id',
        'delivery_chain_id',
        'instance_status',
        'deployment_path',
        'visible',
        'version'
    ];
}
