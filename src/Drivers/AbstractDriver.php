<?php

namespace Imarishwa\MpesaBridge\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException;
use Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException;

abstract class AbstractDriver
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $config;

    /**
     * @var string
     */
    protected $base64MpesaCredentials;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * Create a new driver instance.
     *
     * @return void
     * @throws \Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     */
    public function __construct()
    {
        $this->config = config('mpesa');
        $this->base64MpesaCredentials = $this->base64MpesaCredentials();
        $this->accessToken = $this->mpesaAuth();
    }

    /**
     * Get a valid Mpesa API access token.
     *
     * @return mixed
     *
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     */
    final private function authenticate()
    {
        try {
           $credential = $this->mpesaAuth();

           return $credential->access_token;

        } catch (RequestException $exception) {
            return $exception;
        }
    }

    /**
     * Returns a base64 encoded concatenation of the Mpesa API consumer key and consumer secret.
     *
     * @return string $base64MpesaCredentials
     * @throws \Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException
     */
    final private function base64MpesaCredentials()
    {
        $base64MpesaCredentials = $this->generateCredentials();

        return $base64MpesaCredentials;
    }

    /**
     * Generate a concatenated base64 encoded consumer key and consumer secret.
     *
     * @return string
     * @throws \Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException
     */
    final private function generateCredentials()
    {
        if (isset($this->config['consumer_key']) &&
            isset($this->config['consumer_secret'])) {

            $consumerKey = trim($this->config['consumer_key']);
            $consumerSecret = trim($this->config['consumer_secret']);

            return \base64_encode($consumerKey.':'.$consumerSecret);
        }

        throw new InvalidMpesaApiCredentialsException;

    }

    /**
     * @return string
     *
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     */
    final private function getApiBaseUrl()
    {
        if (isset($this->config['consumer_key']) &&
            isset($this->config['consumer_secret'])) {

            if ($this->config['live'] == true) {
                return $this->config['production_endpoint'];
            } else {
                return $this->config['sandbox_endpoint'];
            }

        }
        throw new MissingBaseApiDomainException;
    }

    /**
     * @return mixed
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     */
    final private function mpesaAuth()
    {
        $client = new Client();

        $response =  $client->request('GET', $this->getApiBaseUrl().MPESA_AUTH_URL, [
            'headers' => [
                'Authorization' => 'Basic '.$this->base64MpesaCredentials,
                'Accept'     => 'application/json'
            ],
        ]);

        $body = \json_decode($response->getBody());

        return $body->access_token;
    }
}
