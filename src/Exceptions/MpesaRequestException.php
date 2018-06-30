<?php

namespace Imarishwa\MpesaBridge\Exceptions;

class MpesaRequestException extends \Exception
{
    protected $requestID;
    protected $errorCode;
    protected $errorMessage;

    public function __construct($message = '', $code = 0, \Exception $previous = null, $requestID = null, $errorCode = null, $errorMessage = null)
    {
        $this->requestID = $requestID;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        parent::__construct($message, $code, $previous);
    }

    public function getRequestID()
    {
        return $this->requestID;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
