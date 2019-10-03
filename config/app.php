<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Lumen'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

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
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',


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
        'hosts' => explode(',', env('ELASTIC_HOSTS'))
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail settings
    |--------------------------------------------------------------------------
    |
    */
    // send notification mails for problems
    'admin-mails' => explode(',', env('ADMIN_MAILS', 'phpid@codixfr.private')),

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
        'password' => env('USER_MANAGEMENT_PASSWORD')
    ],

    'certificates' => [
        'check_expiry' => env('CERTIFICATES_CHECK_EXP', '+30 days')
    ],
    
    'repository' => [
        'username'   => env('REPO_USERNAME'),
        'public_key' => env('REPO_PUBLIC_KEY', 'ssh/id_rsa_repository')
    ],

    'raml2html' => [
        'host'     => env('RAML2HTML_HOST'),
        'username' => env('RAML2HTML_USERNAME'),
        'password' => env('RAML2HTML_PASSWORD'),
    ],

    'dev-management-url' => env('DEV_MANAGEMENT_URL'),

    'hg-build' => [
        'host'     => env('HG_BUILD_HOST'),
        'username' => env('HG_BUILD_USERNAME'),
        'password' => env('HG_BUILD_PASSWORD'),
    ],
];
