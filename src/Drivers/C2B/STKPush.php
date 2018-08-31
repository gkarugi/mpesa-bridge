<?php

namespace Imarishwa\MpesaBridge\Drivers\C2B;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Imarishwa\MpesaBridge\Drivers\BaseDriver;
use Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException;

class STKPush extends BaseDriver
{
    protected $shortCode;
    protected $shortCodePasskey;
    protected $chargeAmount;
    protected $mobileNumber;
    protected $accountReference;
    protected $transactionDescription;
    protected $stkCallback;

    public function using(int $shortCode, string $passkey)
    {
        if ((\is_numeric($shortCode) && (\strlen($shortCode) == 6)) || \preg_match('/[^A-Za-z0-9]/', $passkey, $matches)) {
            $this->shortCode = $shortCode;
            $this->shortCodePasskey = $passkey;

            return $this;
        }

        throw new \InvalidArgumentException('ShortCode should be numeric and 6 digit in length, password should be alphanumeric');
    }

    public function receive($chargeAmount)
    {
        if (!\is_numeric($chargeAmount)) {
            throw new \InvalidArgumentException('charge amount must be numeric');
        }

        $this->chargeAmount = (int) $chargeAmount;

        return $this;
    }

    public function from($mobileNumber)
    {
        if (!\starts_with($mobileNumber, '2547')) {
            throw new \InvalidArgumentException('The mobile number is invalid. Must start with 2547');
        }

        $this->mobileNumber = (string) $mobileNumber;

        return $this;
    }

    public function accountReference(string $accountReference)
    {
        \preg_match('/[^A-Za-z0-9]/', $accountReference, $matches);

        if (\count($matches)) {
            throw new \InvalidArgumentException('Account reference must be alphanumeric.');
        }

        $this->accountReference = $accountReference;

        return $this;
    }

    public function transactionDescription(string $description)
    {
        \preg_match('/[^A-Za-z0-9_ ]/', $description, $matches);

        if (\count($matches)) {
            throw new \InvalidArgumentException('Transaction description must be alphanumeric.');
        }

        $this->transactionDescription = $description;

        return $this;
    }

    public function callbackUrl(string $stkCallbackUrl)
    {
        $this->stkCallback = $stkCallbackUrl;

        return $this;
    }

    public function paramsValid() : bool
    {
        if (is_null($this->mobileNumber) || is_null($this->chargeAmount) || is_null($this->accountReference) || is_null($this->transactionDescription) || is_null($this->stkCallback)) {
            return false;
        }

        return true;
    }

    /**
     * @throws MissingBaseApiDomainException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Imarishwa\MpesaBridge\Exceptions\MpesaRequestException
     *
     * @return mixed
     */
    public function push()
    {
        if (is_null($this->shortCode) || is_null($this->shortCodePasskey) || is_null($this->stkCallback)) {
            if ((stringNotNullOrEmpty($this->config['lnmo_default_short_code']) ||
                stringNotNullOrEmpty($this->config['lnmo_default_passkey'])) === false) {
                throw new \InvalidArgumentException('Shortcode, stk_callback or passkey missing');
            }
            $this->shortCode = $this->config['lnmo_default_short_code'];
            $this->shortCodePasskey = $this->config['lnmo_default_passkey'];
        }

        if (!$this->paramsValid()) {
            throw new \InvalidArgumentException('A safaricom number, an amount, an account reference and transaction description parameters are mandatory. Also ensure a stk push callback url is defined');
        }

        try {
            $response = $this->buildRequest();

            return \json_decode($response->getBody(), true);
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @throws MissingBaseApiDomainException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function buildRequest()
    {
        $time = Carbon::now()->format('YmdHis');
        $base64Password = \base64_encode($this->shortCode.$this->shortCodePasskey.$time);

        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->authenticate(),
                'Accept'        => 'application/json',
            ],
            'json' => [
                'BusinessShortCode' => $this->shortCode,
                'Password'          => $base64Password,
                'Timestamp'         => $time,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $this->chargeAmount,
                'PartyA'            => $this->mobileNumber,
                'PartyB'            => $this->shortCode,
                'PhoneNumber'       => $this->mobileNumber,
                'CallBackURL'       => $this->stkCallback,
                'AccountReference'  => $this->accountReference,
                'TransactionDesc'   => $this->transactionDescription,
            ],
        ]);

        $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_STK_PUSH_URL));

        return $response;
    }
}
