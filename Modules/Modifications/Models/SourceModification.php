<?php

namespace Modules\Modifications\Models;

class SourceModification extends Modification
{
    protected static $type = 'source';

    protected $visible = [
        'id',
        'name',
        'type',
        'issue',
        'path',
        'deliveryChain',
        'version',
        'prev_version',
        'instance',
        'actionType',
        'instanceStatus',
        'comments',
        'permissions',
        'header_only'
    ];
}
