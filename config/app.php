<?php
$app = require __DIR__ . '/../vendor/laravel/lumen-framework/config/app.php';

return array_merge(
    $app,
    [
        'timezone' => env('APP_TIMEZONE'),

        /*
        |--------------------------------------------------------------------------
        | Application code
        |--------------------------------------------------------------------------
        | User Management application code. Used to get application info from DB
        |
        */
        'application_code' => env('APP_CODE'),

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

        /*
        |--------------------------------------------------------------------------
        | Elastic Search
        |--------------------------------------------------------------------------
        |
        | Here you may configure the elastic search settings for your application.
        |
        */
        'elastic' => [
            'hosts' => [
                [
                    'host'   => env('ELASTIC_HOST'),
                    'port'   => env('ELASTIC_PORT', '9200'),
                    'scheme' => env('ELASTIC_SCHEME', 'http'),
                    'user'   => env('ELASTIC_USERNAME'),
                    'pass'   => env('ELASTIC_PASSWORD')
                ]
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Mail settings
        |--------------------------------------------------------------------------
        |
        */
        // send notification mails for problems
        'admin-mails' => explode(',', env('ADMIN_MAILS', 'phpid@codixfr.private')),

         // send notification mails to PMO management group
         'pmo-management-mails' => explode(',', env('PMO_MANAGEMENT_MAIL', 'TEST_MAILS')),


        // if not production all mails will be send to this addresses
        'test-mails'  => explode(',', env('TEST_MAILS', 'phpid@codixfr.private')),

        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        |
        | Connection to user management system for login and automation requests
        |
        */
        'user-management' => [
            'url'      => env('USER_MANAGEMENT_URL'),
            'username' => env('USER_MANAGEMENT_USERNAME'),
            'password' => env('USER_MANAGEMENT_PASSWORD'),
            'code'     => env('USER_MANAGEMENT_CODE')
        ],

        'certificates' => [
            'check_expiry' => env('CERTIFICATES_CHECK_EXP', '+30 days')
        ],

        'dev-management-url' => env('DEV_MANAGEMENT_URL'),

        /*
        |--------------------------------------------------------------------------
        | Binary repository information
        |--------------------------------------------------------------------------
        |
        | Binary repository configuration - Nexus and Artifactory
        |
        */
        'artifactory' => [
            'url' => env('ARTIFACTORY_URL'),
            'key' => env('ARTIFACTORY_KEY'),
        ],
        'nexus' => [
            'host'        => env('NEXUS_HOST'),
            'port'        => env('NEXUS_PORT', 80),
            'se_repo'     => env('NEXUS_SE_REPO'),
            'rhode_url'   => env('NEXUS_RHODE_URL'),
            'se_repo_url' => env('NEXUS_SE_REPO_URL'),
        ],

        /*
        |--------------------------------------------------------------------------
        | SSH information
        |--------------------------------------------------------------------------
        |
        | SSH configuration for codix internal instances
        |
        */
        'ssh' => [
            'host'        => env('SSH_HOST', 'bira.codixfr.private'),
            'port'        => env('SSH_PORT', 22),
            'username'    => env('SSH_USERNAME'),
            'password'    => env('SSH_PASSWORD'),
            'public_key'  => env('SSH_KEY'),
            'tnsname_host'=> env('TNSNAME_CONFIG_HOST', 'asparuh.codixfr.private'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Oci information
        |--------------------------------------------------------------------------
        |
        | SSH configuration for codix internal instances
        |
        */
        'oci' => [
            'username'   => env('OCI_USERNAME'),
            'password'   => env('OCI_PASSWORD'),
        ],

        'ddl' => [
            'repo-url' => env('DDL_REPO_URL'),
            'repo-name' => env('DDL_REPO_NAME'),
        ]
    ]
);
