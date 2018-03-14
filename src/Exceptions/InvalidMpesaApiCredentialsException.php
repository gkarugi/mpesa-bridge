<?php

namespace Imarishwa\MpesaBridge\Exceptions;

class InvalidMpesaApiCredentialsException extends \Exception
{
    protected $message = 'Consumer key or consumer secret are missing';
}
