<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mpesa App Environment
    |--------------------------------------------------------------------------
    |
    | Determines the environment that will be used by Mpesa.
    | can be either production(For live apps already vetted by safaricom and ready
    | to handle live transactions) or sandbox(For staging, testing and apps still in development)
    |
    | Possible values: sandbox | production
    | Default: sandbox
    */
    'mpesa_env' => env('MPESA_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | These are the credentials provided by safaricom to authenticate with the M-Pesa API
    | so as to be able to transact
    */
    'consumer_key'        => env('MPESA_CONSUMER_KEY'),
    'consumer_secret'     => env('MPESA_CONSUMER_SECRET'),

    'production_endpoint' => 'https://api.safaricom.co.ke',
    'sandbox_endpoint'    => 'https://sandbox.safaricom.co.ke',

    'default_initiator_name'                => env('MPESA_DEFAULT_INITIATOR_NAME'),
    'default_initiator_short_code'          => env('MPESA_DEFAULT_INITIATOR_SHORT_CODE'),
    'default_initiator_password'            => env('MPESA_DEFAULT_INITIATOR_PASSWORD'),
    'default_initiator_security_credential' => env('MPESA_DEFAULT_INITIATOR_SECURITY_CREDENTIAL'),
    'simulate_payment_test_mobile_number'   => env('MPESA_SIMULATE_TEST_MOBILE_NUMBER', '254708374149'),

    /*
    |--------------------------------------------------------------------------
    | Lipa Na Mpesa Online Short Code Paybill Number (LNMO)
    |--------------------------------------------------------------------------
    |
    | This is a registered Paybill Number that will be used as the Merchant ID
    | on every transaction. This is also the account to be debited.
    |
    */
    'lnmo_default_short_code' => env('MPESA_DEFAULT_LNMO_SHORT_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Lipa Na Mpesa Online Short Code Passkey
    |--------------------------------------------------------------------------
    |
    | This is the secret SAG Passkey generated by Safaricom on registration
    | of the Merchant's Paybill Number.
    |
    */
    'lnmo_default_passkey' => env('MPESA_DEFAULT_LNMO_PASSKEY'),

    /*
    |--------------------------------------------------------------------------
    | STK Callback URL
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */
    'stk_callback' => env('MPESA_STK_CALLBACK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Confirmation URL
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */
    'confirmation_url' => env('MPESA_CONFIRMATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Validation URL
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API to validate transaction.
    |
    */
    'validation_url' => env('MPESA_VALIDATION_URL'),

    /*
   |--------------------------------------------------------------------------
   | Queue timeout URL
   |--------------------------------------------------------------------------
   |
   | This is a fully qualified endpoint that will be be queried by Safaricom's
   | API in case of a queue timeout.
   |
   */
    'queue_timeout_url' => env('MPESA_QUEUE_TIMEOUT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Identity Validation Callback URL
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */
    'identity_validation_callback' => '',
];
