<?php

namespace Modules\Modifications\Models;

class CommandModification extends Modification
{
    protected static $type = 'cmd';

    protected $visible = [
        'id',
        'type',
        'type_id',
        'issue',
        'issue_id',
        'delivery_chain_id',
        'name',
        'comments',
        'check_exit_status',
        'subtype',
        'instance',
        'instance_status',
        'est_run_time',
        'visible'
    ];
}
