<?php

/*
|--------------------------------------------------------------------------
| URLS
|--------------------------------------------------------------------------
| Mpesa API endpoints
*/
const MPESA_AUTH_URL = 'oauth/v1/generate?grant_type=client_credentials';
const MPESA_ID_CHECK_URL = 'mpesa/checkidentity/v1/query';
const MPESA_REGISTER_URL = 'mpesa/c2b/v1/registerurl';
const MPESA_SIMULATE_URL = 'mpesa/c2b/v1/simulate';
const MPESA_STK_PUSH_URL = 'mpesa/stkpush/v1/processrequest';
const MPESA_STK_PUSH_VALIDATE_URL = 'mpesa/stkpushquery/v1/query';
const MPESA_BASE_SANDBOX_URL = 'https://sandbox.safaricom.co.ke/';
