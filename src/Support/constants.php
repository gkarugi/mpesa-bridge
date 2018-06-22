<?php

/*
|--------------------------------------------------------------------------
| URLS
|--------------------------------------------------------------------------
| Mpesa API endpoints
*/
const MPESA_AUTH_URL = '/oauth/v1/generate?grant_type=client_credentials';
const MPESA_ID_CHECK_URL = '/mpesa/checkidentity/v1/query';
const MPESA_C2B_SIMULATE_URL = '/mpesa/c2b/v1/simulate';
const MPESA_C2B_REGISTER_URL = '/mpesa/c2b/v1/registerurl';
const MPESA_STK_PUSH_URL = '/mpesa/stkpush/v1/processrequest';
const MPESA_STK_PUSH_VALIDATE_URL = '/mpesa/stkpushquery/v1/query';
const MPESA_BASE_SANDBOX_URL = 'https://sandbox.safaricom.co.ke/';

const MPESA_B2C_URL = '/mpesa/b2c/v1/paymentrequest';
const MPESA_B2B_URL = '/mpesa/b2b/v1/paymentrequest';
const MPESA_ACCOUNT_BALANCE_URL = '/mpesa/accountbalance/v1/query';
const MPESA_TRANSACTION_STATUS_URL = '/mpesa/transactionstatus/v1/query';
const MPESA_REVERSAL_URL = '/mpesa/reversal/v1/request';
