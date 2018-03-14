<?php

namespace Imarishwa\MpesaBridge\Exceptions;

class MissingBaseApiDomainException extends \Exception
{
    protected $message = 'Missing the API production or sandbox domain endpoint';
}
