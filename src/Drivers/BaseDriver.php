<?php

namespace Imarishwa\MpesaBridge\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException;
use Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException;
use Imarishwa\MpesaBridge\Exceptions\MpesaRequestException;

class BaseDriver
{
//    TODO: setup cache so that a new token is only requested when it is near expiration

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
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * Create a new driver instance.
     *
     * @throws \Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = config('mpesa');
        $this->base64MpesaCredentials = $this->base64MpesaCredentials();
        $this->apiBaseUrl = $this->getApiBaseUrl();
        $this->accessToken = $this->mpesaAuth();
    }

    /**
     * Get a valid Mpesa API access token.
     *
     * @return mixed
     */
    public function authenticate()
    {
        try {
            $accessToken = $this->accessToken;

            return $accessToken;
        } catch (RequestException $exception) {
            return $exception;
        }
    }

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     *
     * @return mixed
     */
    final private function mpesaAuth()
    {
        $client = new Client();

        $response = $client->request('GET', $this->getApiBaseUrl().MPESA_AUTH_URL, [
            'headers' => [
                'Authorization' => 'Basic '.$this->base64MpesaCredentials,
                'Accept'        => 'application/json',
            ],
        ]);

        $body = \json_decode($response->getBody());

        return $body->access_token;
    }

    /**
     * Returns a base64 encoded concatenation of the Mpesa API consumer key and consumer secret.
     *
     * @throws \Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException
     *
     * @return string $base64MpesaCredentials
     */
    final private function base64MpesaCredentials()
    {
        $base64MpesaCredentials = $this->generateCredentials();

        return $base64MpesaCredentials;
    }

    /**
     * Generate a concatenated base64 encoded consumer key and consumer secret.
     *
     * @throws \Imarishwa\MpesaBridge\Exceptions\InvalidMpesaApiCredentialsException
     *
     * @return string
     */
    final private function generateCredentials()
    {
        if (stringNotNullOrEmpty($this->config['consumer_key']) &&
            stringNotNullOrEmpty($this->config['consumer_secret'])) {
            $consumerKey = trim($this->config['consumer_key']);
            $consumerSecret = trim($this->config['consumer_secret']);

            return \base64_encode($consumerKey.':'.$consumerSecret);
        }

        throw new InvalidMpesaApiCredentialsException();
    }

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     *
     * @return string
     */
    public function getApiBaseUrl()
    {
        if (stringNotNullOrEmpty($this->config['production_endpoint']) &&
            stringNotNullOrEmpty($this->config['sandbox_endpoint'])) {
            if ($this->config['mpesa_env'] == 'production') {
                return $this->config['production_endpoint'];
            } else {
                return $this->config['sandbox_endpoint'];
            }
        }

        throw new MissingBaseApiDomainException('Missing BaseApiDomain. Check your config.');
    }

    /**
     * @param RequestException $exception
     *
     * @throws MpesaRequestException
     */
    public function handleException(RequestException $exception)
    {
        $mpesaResponseData = json_decode($exception->getResponse()->getBody()->getContents());
        $requestID = $mpesaResponseData->requestId ?? null;
        $errorCode = $mpesaResponseData->errorCode ?? null;
        $errorMessage = $mpesaResponseData->errorMessage ?? null;
        $reasonPhrase = $exception->getResponse()->getReasonPhrase();
        $statusCode = $exception->getResponse()->getStatusCode();

        throw new MpesaRequestException($reasonPhrase, $statusCode, null, $requestID, $errorCode, $errorMessage);
    }
}
