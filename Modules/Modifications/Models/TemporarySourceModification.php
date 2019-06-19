<?php

namespace Modules\Modifications\Models;

class TemporarySourceModification extends Modification
{
    protected static $type = 'tmpsrc';

    protected $visible = [
        'id',
        'issue',
        'type',
        'title',
        'name',
        'comments',
        'subtype',
        'instanceStatus',
        'version'
    ];
}
