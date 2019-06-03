<?php

namespace Modules\Modifications\Models;

class OperationModification extends Modification
{
    protected static $type = 'oper';

    protected $visible = [
        'id',
        'issue',
        'subtype',
        'deliveryChain',
        'instanceStatus'
    ];
}
