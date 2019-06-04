<?php

namespace Modules\Modifications\Models;

class CommandModification extends Modification
{
    protected static $type = 'cmd';

    protected $visible = [
        'id',
        'issue',
        'name',
        'comments',
        'check_exit_status',
        'subtype',
        'instance',
        'instanceStatus',
        'est_run_time'
    ];
}
