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
        'path',
        'maven_repository',
        'deployment_prefix_id',
        'deployment_prefix',
        'instance_status',
        'active',
        'visible'
    ];
}
