<?php

namespace Modules\Modifications\Models;

class CmsModification extends Modification
{
    protected static $type = 'cms';

    protected $visible = [
        'id',
        'issue',
        'issue_id',
        'subtype',
        'subtype_id',
        'type',
        'type_id',
        'version',
        'revision_converted',
        'name',
        'delivery_chain_id',
        'deliveryChain',
        'instanceStatus',
        'active',
        'visible',
        'contents',
        'path_id'
    ];
}
