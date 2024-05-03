<?php

return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => env('GOOGLE_APPLICATION_NAME', ''),

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id' => env('GOOGLE_CLIENT_ID', ''),
    'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
    'redirect_uri' => env('GOOGLE_REDIRECT', ''),
    'scopes' => [\Google\Service\Sheets::DRIVE, \Google\Service\Sheets::SPREADSHEETS],
    'access_type' => 'online',
    'approval_prompt' => 'auto',

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => env('GOOGLE_DEVELOPER_KEY', ''),

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Set the credentials JSON's location to use assert credentials, otherwise
    | app engine or compute engine will be used.
    |
    */
    'service' => [
        /*
        | Enable service account auth or not.
        */
        'enable' => env('GOOGLE_SERVICE_ENABLED', false),

        /*
         * Path to service account json file. You can also pass the credentials as an array
         * instead of a file path.
         */
        // 'file' => storage_path('decoded-tribute-420707-a0d01d65e2c7.json'),
        'file' => [
            "type" => "service_account",
            "project_id" => env('GOOGLE_PROJECT_ID'),
            "private_key_id" => env('GOOGLE_PRIVATE_KEY_ID'),
            "private_key" => env('GOOGLE_PRIVATE_KEY'),
            "client_email" => env('GOOGLE_CLIENT_EMAIL'),
            "client_id" => env('GOOGLE_CLIENT_ID'),
            "auth_uri" => env('GOOGLE_AUTH_URI'),
            "token_uri" => env('GOOGLE_TOKEN_URI'),
            "auth_provider_x509_cert_url" => env('GOOGLE_AUTH_PROVIDER_X509_CERT_url'),
            "client_x509_cert_url" => env('GOOGLE_CLIENT_X509_CERT_URL'),
            "universe_domain" => env('GOOGLE_UNIVERSE_DOMAIN')
        ],
    ],

    /*
    |----------------------------------------------------------------------------
    | Additional config for the Google Client
    |----------------------------------------------------------------------------
    |
    | Set any additional config variables supported by the Google Client
    | Details can be found here:
    | https://github.com/google/google-api-php-client/blob/master/src/Google/Client.php
    |
    | NOTE: If client id is specified here, it will get over written by the one above.
    |
    */
    'config' => [],
];
