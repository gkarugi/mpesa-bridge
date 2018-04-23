<?php

namespace Imarishwa\MpesaBridge\Drivers\C2B;

use App\Exceptions\InvalidCredentialsException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Imarishwa\MpesaBridge\Drivers\BaseDriver;
use Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException;

class STKPush extends BaseDriver
{
    protected $shortCode;
    protected $shortCodePassword;
    protected $chargeAmount;
    protected $safaricomNumber;
    protected $accountReference;
    protected $transactionDescription;
    protected $stkCallback;

    public function using(int $shortCode, string $password)
    {
        if (!\is_numeric($shortCode) || \preg_match('/[^A-Za-z0-9]/', $password, $matches)) {
            throw new \InvalidArgumentException('Short code should be numeric and password should be alphanumeric');
        }

        $this->shortCode = $shortCode;
        $this->shortCodePassword = $password;

        return $this;
    }

    public function receive($chargeAmount)
    {
        if (!\is_numeric($chargeAmount)) {
            throw new \InvalidArgumentException('charge amount must be numeric');
        }
        $this->chargeAmount = (int) $chargeAmount;

        return $this;
    }

    public function from($safaricomNumber)
    {
        if (!starts_with($safaricomNumber, '2547')) {
            throw new \InvalidArgumentException('The number must be a safaricom number');
        }
        $this->safaricomNumber = (string) $safaricomNumber;

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

    public function paramsValid() : bool
    {
        if (is_null($this->safaricomNumber) || is_null($this->chargeAmount) || is_null($this->accountReference) || is_null($this->transactionDescription) || is_null($this->stkCallback)) {
            return false;
        }

        return true;
    }

    public function push()
    {
        if (is_null($this->shortCode) || is_null($this->shortCodePassword) || is_null($this->stkCallback)) {
            if ((stringNotNullOrEmpty($this->config['lnmo_default_short_code']) ||
                stringNotNullOrEmpty($this->config['lnmo_default_passkey']) ||
                stringNotNullOrEmpty($this->config['stk_callback'])) === false) {
                throw new \InvalidArgumentException('Shortcode, stk_callback or passkey missing');
            }
            $this->shortCode = $this->config['lnmo_default_short_code'];
            $this->shortCodePassword = $this->config['lnmo_default_passkey'];
            $this->stkCallback = $this->config['stk_callback'];
        }

        if (!$this->paramsValid()) {
            throw new \InvalidArgumentException('A safaricom number, an amount, an account reference and transaction description parameters are mandatory. Also ensure a stk_callback is defined');
        }

        try {
            return $this->buildRequest();
        } catch (InvalidCredentialsException $e) {
        } catch (MissingBaseApiDomainException $e) {
        }
    }

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     * @throws \App\Exceptions\InvalidCredentialsException
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
                'BusinessShortCode' => $this->shortCode,
                'Password'          => $base64Password,
                'Timestamp'         => $time,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $this->chargeAmount,
                'PartyA'            => $this->safaricomNumber,
                'PartyB'            => $this->shortCode,
                'PhoneNumber'       => $this->safaricomNumber,
                'CallBackURL'       => $this->config['stk_callback'],
                'AccountReference'  => $this->accountReference,
                'TransactionDesc'   => $this->transactionDescription,
            ],
        ]);

        $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_STK_PUSH_URL));

        return (array) \json_decode($response->getBody());
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
    public function validateTransaction($checkoutRequestID)
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
