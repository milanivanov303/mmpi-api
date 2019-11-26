<?php

return [

    'tags' => [
        'users'               => 'Users',
        'hashes'              => 'Hashes',
        'issues'              => 'Issues',
        'enum-values'         => 'Enum Values',
        'projects'            => 'Projects',
        'project-events'      => 'Project Events',
        'instances'           => 'Instances',
        'installations'       => 'Installations',
        'delivery-chains'     => 'Delivery Chains',
        'modifications'       => 'Modifications',
        'patch-requests'      => 'Patch Requests',
        'patches'             => 'Patches',
        'certificates'        => 'Certificates',
        'jsonrpc'             => 'Json RPC',
        'instance-downtimes'  => 'Instance Downtimes',
        'branches'            => 'Branches',
        'project-specifics'   => 'Project Specifics',
        'sources'             => 'Sources',
        'source-dependencies' => 'Source Dependencies',
        'source-revisions'    => 'Source Revisions',
        'departments'         => 'Departments',
    ],

    'summary' => [
        'list'   => 'List :name',
        'one'    => 'Get :name',
        'create' => 'Create :name',
        'update' => 'Update :name',
        'delete' => 'Delete :name',
    ],

    'enum-values' => [
        'one'    => [
            'type_and_key' => [
                'summary' => 'Get Enum Value by type and key'
            ]
        ],
        'create' => [
            'type_and_key' => [
                'summary' => 'Create Enum Value by type and key'
            ]
        ],
        'update' => [
            'type_and_key' => [
                'summary' => 'Update Enum Value by type and key'
            ]
        ],
        'delete' => [
            'type_and_key' => [
                'summary' => 'Delete Enum Value by type and key'
            ]
        ],
    ],

    'jsonrpc' => [
        'summary' => 'Json RPC'
    ]
];
