<?php

namespace Modules\Modifications\Models;

class SeTransferModification extends Modification
{
    protected static $type = 'se';

    protected $visible = [
        'id',
        'type',
        'subtype',
        'instance',
        'instanceStatus'
    ];
}
