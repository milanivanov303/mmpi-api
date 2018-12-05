<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'hdW9yZGFm5hdWRvdXJ5RvdioiWWIjoiWW9yZGFm5hdW9'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */
    'locale' => env('APP_LOCALE', 'en'),
    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | JWT settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the ldSWT settings for your application.
    |
    */
    'jwt' => [
        'secret_key'  => env('JWT_SECRET_KEY', 'I6NTEyLCJuYW1lIjoiWW9yZGFuICBBcm5hdWRvdiIsInVzZXJuY1ZG9'),
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
    ]
];
