<?php

namespace Imarishwa\MpesaBridge\Drivers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class STKPush extends AbstractDriver
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

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     * @throws \App\Exceptions\InvalidCredentialsException
     */
    public function buildRequest()
    {
        $time = Carbon::now()->format('YmdHis');
        $shortCode = $this->config['short_code'];
        $passkey = $this->config['passkey'];
        $password = \base64_encode($shortCode.$passkey.$time);

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->authenticate(),
                'Accept'        => 'application/json',
            ],
            'json' => [
                'BusinessShortCode' => $this->config['short_code'],
                'Password'          => $password,
                'Timestamp'         => $time,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => 20,
                'PartyA'            => '254727357218',
                'PartyB'            => $shortCode,
                'PhoneNumber'       => '254727357218',
                'CallBackURL'       => 'https://revenue.localtunnel.me/callback',
                'AccountReference'  => 'Imarishwa County',
                'TransactionDesc'   => 'payment for county',
            ],
        ]);

        $response = $client->send(new Request($this->config['callback_method'], $this->getApiBaseUrl().MPESA_STK_PUSH_URL));
        $body = \json_decode($response->getBody());
        dd($body);
    }

    /**
     * Validate an initialized transaction.
     *
     * @param string $checkoutRequestID
     *
     * @throws
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate($checkoutRequestID)
    {
        $time = Carbon::now()->format('YmdHis');
        $shortCode = $this->config['short_code'];
        $passkey = $this->config['passkey'];
        $password = \base64_encode($shortCode.$passkey.$time);

        $body = [
            'BusinessShortCode'   => $shortCode,
            'Password'            => $password,
            'Timestamp'           => $time,
            'CheckoutRequestID'   => $checkoutRequestID,
        ];

        try {
            $response = $this->makeRequest($body, $this->getApiBaseUrl().MPESA_STK_PUSH_VALIDATE_URL);
            $client = new Client([
                'headers' => [
                    'Authorization' => 'Bearer '.$this->authenticate(),
                    'Accept'        => 'application/json',
                ],
                'json' => $body,
            ]);

            $response = $client->send(new Request($this->config['callback_method'], $this->getApiBaseUrl().MPESA_STK_PUSH_URL));

            return \json_decode($response->getBody());
        } catch (RequestException $exception) {
            return \json_decode($exception->getResponse()->getBody());
        }
    }
}
