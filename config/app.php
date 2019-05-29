<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the ldSWT settings for your application.
    |
    */
    'jwt' => [
        'public_key' => env('JWT_PUBLIC_KEY', 'ssh/id_rsa.pub'),
        'algorithms' => explode(',', env('JWT_ALGORITHMS', 'RS256'))
    ],

    /*
    |--------------------------------------------------------------------------
    | Ldap
    |--------------------------------------------------------------------------
    |
    | Here you may configure the ldap settings for your application.
    |
    */
    'ldap' => [

        // An array of your LDAP hosts. You can use either
        // the host name or the IP address of your host.
        'hosts'    => explode(',', env('LDAP_HOSTS')),

        // The port to use for connecting to your hosts.
        'port'     => env('LDAP_PORT'),

        // Whether or not to use SSL when connecting to your hosts.
        'use_ssl' => env('LDAP_USE_SSL'),

        // The base distinguished name of your domain to perform searches upon.
        'base_dn'  => env('LDAP_BASE_DN'),

        // The account to use for querying / modifying LDAP records. This
        // does not need to be an admin account. This can also
        // be a full distinguished name of the user account.
        'username' => env('LDAP_USERNAME'),
        'password' => env('LDAP_PASSWORD')
    ],

    'elastic' => [
        'hosts' => explode(',', env('ELASTIC_HOSTS'))
    ],

    'users_sync_mail' => env('USERS_SYNC_MAIL', 'phpid@codix.bg'),

    'test_mails' => explode(',', env('TEST_MAILS', 'phpid@codix.bg')),

    'user-management' => [
        'url'      => env('USER_MANAGEMENT_URL'),
        'username' => env('USER_MANAGEMENT_USERNAME'),
        'password' => env('USER_MANAGEMENT_PASSWORD')
    ],

    'repository' => [
        'username'   => env('REPO_USERNAME'),
        'public_key' => env('REPO_PUBLIC_KEY', 'ssh/id_rsa_repository')
    ]
];
