<?php

namespace Modules\Modifications\Models;

class BinaryModification extends Modification
{
    protected static $type = 'binary';

    protected $visible = [
        'id',
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
