<?php

namespace Imarishwa\MpesaBridge\Drivers;

class STKPush extends AbstractDriver
{
    /**
     * SMS message.
     *
     * @var string
     */
    protected $message;

    /**
     * Specify the SMS message.
     *
     * @pram string $message
     *
     * @return $this
     */
    public function message($message)
    {
        dd($this->accessToken);
        $this->message = $message.' Here';

        return $this;
    }
}
