<?php

namespace Modules\Modifications\Models;

class SourceModification extends Modification
{
    protected static $type = 'source';

    protected $visible = [
        'id',
        'type',
        'issue',
        'path',
        'deliveryChain',
        'version',
        'prev_version',
        'instance',
        'actionType',
        'targetSchema',
        'instanceStatus',
        'comments',
        'permissions'
    ];
}
