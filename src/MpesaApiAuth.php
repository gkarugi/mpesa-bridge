<?php

namespace Imarishwa\MpesaBridge;

class MpesaApiAuth
{
//    private $config;

    public function __construct()
    {
    }

    /**
     * Generate the base64 encoded authorization key.
     *
     * @return string
     */
    public static function generateCredentials()
    {
        $config = __DIR__.'/../../config/mpesa.php';
        dd($config);
//        $consumerKey    = $this->engine->config->get('mpesa.consumer_key');
//        $consumerSecret = $this->engine->config->get('mpesa.consumer_secret');

//        return \base64_encode($key . ':' . $secret);
    }
}
