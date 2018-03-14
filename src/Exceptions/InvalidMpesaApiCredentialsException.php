<?php

namespace Imarishwa\MpesaBridge\Exceptions;

class MissingMpesaApiCredentialsException extends \Exception
{
    protected $message = 'Consumer key or consumer secret are missing';
}
