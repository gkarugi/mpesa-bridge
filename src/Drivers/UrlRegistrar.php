<?php

namespace Imarishwa\MpesaBridge\Drivers;

class UrlRegistrar extends BaseDriver
{
    /**
     * SMS message.
     *
     * @var string
     */
    protected $message;

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     */
    public function message()
    {
        dd($this->buildRequest());
    }
}
