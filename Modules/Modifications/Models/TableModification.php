<?php

namespace Modules\Modifications\Models;

class TableModification extends Modification
{
    protected static $type = 'table';

    protected $visible = [
        'id',
        'issue',
        'type',
        'name',
        'tablespace',
        'targetSchema',
        'instanceStatus',
        'subtype'
    ];
}
