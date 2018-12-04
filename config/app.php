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
        'secret_key'  => env('JWT_SECRET_KEY', '66859BB88A7AD5214DF71CDCFA27DFF2EFCC80A81E9BA5FA0'),
        'algorithm'   => env('JWT_ALGORITHM', 'HS256'),
        'exp'         => env('JWT_EXP', '+1 hour'),
        'refresh_exp' => env('JWT_REFRESH_EXP', '+1 day')
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
        'hosts'    => ['cxdcbg1.codixfr.private', 'cxdcbg2.codixfr.private'],

        // The port to use for connecting to your hosts.
        'port'     => 636,

        // Whether or not to use SSL when connecting to your hosts.
        'use_ssl' => true,

        // The base distinguished name of your domain to perform searches upon.
        'base_dn'  => 'OU=Codix FR Users,DC=CODIXFR,DC=PRIVATE',

        // The account to use for querying / modifying LDAP records. This
        // does not need to be an admin account. This can also
        // be a full distinguished name of the user account.
        'username' => 'postservice postfix',
        'password' => 'Qwerty321'
    ]
];
