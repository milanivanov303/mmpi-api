<?php

namespace Modules\Modifications\Models;

class SourceModification extends Modification
{
    protected static $type = 'source';

    protected $visible = [
        'id',
        'name',
        'path',
        'contents',
        'delivery_chain',
        'version',
        'prev_version',
        'revision_converted',
        'comments',
        'permissions',
        'action_type',
        'created_on'
    ];
}
