<?php

namespace Modules\Modifications\Models;

class BinaryModification extends Modification
{
    protected static $type = 'binary';

    protected $visible = [
        'id',
        'type',
        'subtype',
        'issue',
        'path',
        'prev_version',
        'instance',
        'instanceStatus',
        'deployment_path',
        'comments',
        'permissions'
    ];
}
