<?php

namespace Imarishwa\MpesaBridge\Drivers;

use Illuminate\Support\Facades\Config;
use Imarishwa\MpesaBridge\Exceptions\InvalidCredentialsException;

abstract class AbstractDriver
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $config;

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->accessToken = $this->mpesaAuth();
        $this->config = config('mpesa');
    }

    /**
     * Authenticate with the Mpesa API.
     *
     * @return string $accessToken
     */
    final private function mpesaAuth()
    {
        $accessToken = $this->generateCredentials();

        return $accessToken;
    }

    /**
     * Generate the concatenated base64 encoded consumer key and consumer secret.
     *
     * @return string
     */
    private function generateCredentials()
    {
        try {
            $consumerKey = $this->config->get('mpesa.consumer_key');
            $consumerSecret = $this->config->get('mpesa.consumer_secret');
        } catch (InvalidCredentialsException $exception) {
            return $exception;
        }

        return \base64_encode($consumerKey.':'.$consumerSecret);
    }
}
