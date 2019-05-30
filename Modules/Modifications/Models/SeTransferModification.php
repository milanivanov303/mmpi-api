<?php

namespace Modules\Modifications\Models;

class SeTransferModification extends Modification
{
    protected static $type = 'se';

    protected $visible = [
        'id',
        'issue_id',
        'type_id',
        'check_nok',
        'check_ok',
        'subtype_id',
        'instance_id',
        'path_id',
        'name',
        'user_defined_paths',
        'details',
        'instance_status'
    ];
}
