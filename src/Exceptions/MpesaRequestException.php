<?php

namespace Imarishwa\MpesaBridge\Exceptions;


class MpesaRequestException extends \Exception
{
    protected $requestID;
    protected $errorCode;
    protected $errorMessage;

    public function __construct($message = '', $code = 0 , \Exception $previous = NULL, $requestID = NULL, $errorCode = NULL, $errorMessage = NULL)
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