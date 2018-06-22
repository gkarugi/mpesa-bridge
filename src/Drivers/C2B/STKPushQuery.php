<?php

namespace Imarishwa\MpesaBridge\Drivers\C2B;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Imarishwa\MpesaBridge\Drivers\BaseDriver;
use Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException;

class STKPushQuery extends BaseDriver
{
    protected $shortCode;
    protected $shortCodePassword;
    protected $checkoutRequestID;

    public function using(int $shortCode, string $password)
    {
        if (!\is_numeric($shortCode) || \preg_match('/[^A-Za-z0-9]/', $password, $matches)) {
            throw new \InvalidArgumentException('Short code should be numeric and password should be alphanumeric');
        }

        $this->shortCode = $shortCode;
        $this->shortCodePassword = $password;

        return $this;
    }

    public function checkoutRequestID(string $checkoutRequestID)
    {
        $this->checkoutRequestID = $checkoutRequestID;

        return $this;
    }

    public function paramsValid() : bool
    {
        if (is_null($this->checkoutRequestID)) {
            return false;
        }

        return true;
    }

    /**
     * @throws MissingBaseApiDomainException
     *
     * @return \Exception|RequestException|mixed
     */
    public function checkTransactionStatus()
    {
        if (is_null($this->shortCode) || is_null($this->shortCodePassword)) {
            if ((stringNotNullOrEmpty($this->config['lnmo_default_short_code']) ||
                stringNotNullOrEmpty($this->config['lnmo_default_passkey'])) === false) {
                throw new \InvalidArgumentException('Shortcode or passkey missing');
            }
            $this->shortCode = $this->config['lnmo_default_short_code'];
            $this->shortCodePassword = $this->config['lnmo_default_passkey'];
        }

        if (!$this->paramsValid()) {
            throw new \InvalidArgumentException('checkoutRequestID parameter is mandatory');
        }

        return $this->buildRequest();
    }

    /**
     * @throws MissingBaseApiDomainException
     *
     * @return \Exception|RequestException|mixed
     */
    public function buildRequest()
    {
        $time = Carbon::now()->format('YmdHis');
        $base64Password = \base64_encode($this->shortCode.$this->shortCodePassword.$time);

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->authenticate(),
                'Accept'        => 'application/json',
            ],
            'json' => [
                'BusinessShortCode'   => $this->shortCode,
                'Password'            => $base64Password,
                'Timestamp'           => $time,
                'CheckoutRequestID'   => $this->checkoutRequestID,
            ],
        ]);

        try {
            $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_STK_PUSH_VALIDATE_URL));

            return \json_decode($response->getBody(), true);
        } catch (RequestException $exception) {
            return $exception;
        }
    }
}
