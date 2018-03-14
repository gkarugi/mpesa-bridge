<?php

namespace Imarishwa\MpesaBridge\Exceptions;

class MissingBaseApiDomain extends \Exception
{
    protected $message = 'Missing the API production or sandbox domain endpoint';
}