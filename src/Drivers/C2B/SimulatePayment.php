<?php

namespace Imarishwa\MpesaBridge\Drivers\C2B;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Imarishwa\MpesaBridge\Drivers\BaseDriver;

class SimulatePayment extends BaseDriver
{
    protected $shortCode;
    protected $chargeAmount;
    protected $safaricomNumber;
    protected $billReferenceNumber;

    public function using(int $shortCode)
    {
        if (!\is_numeric($shortCode)) {
            throw new \InvalidArgumentException('Short code should be numeric and is required');
        }

        $this->shortCode = $shortCode;

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

    public function billReferenceNumber(string $billReferenceNumber)
    {
        \preg_match('/[^A-Za-z0-9]/', $billReferenceNumber, $matches);

        if (\count($matches)) {
            throw new \InvalidArgumentException('Bill reference number must be alphanumeric.');
        }

        $this->billReferenceNumber = $billReferenceNumber;

        return $this;
    }

    public function paramsValid() : bool
    {
        if (is_null($this->safaricomNumber) || is_null($this->chargeAmount) || is_null($this->shortCode)) {
            return false;
        }

        return true;
    }

    /**
     * @throws MissingBaseApiDomainException
     * @throws \Imarishwa\MpesaBridge\Exceptions\MpesaRequestException
     *
     * @return mixed
     */
    public function simulate()
    {
        if (is_null($this->shortCode)) {
            if ((stringNotNullOrEmpty($this->config['default_initiator_short_code'])) === false) {
                throw new \InvalidArgumentException('Shortcode  missing');
            }
            $this->shortCode = $this->config['default_initiator_short_code'];
        }

        if (!$this->paramsValid()) {
            throw new \InvalidArgumentException('A safaricom number, shortcode and charge amount are mandatory');
        }

        try {
            $response = $this->buildRequest();

            return \json_decode($response->getBody(), true);
        } catch (RequestException $exception) {
            $this->handleException($exception);

            return;
        }
    }

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     */
    public function buildRequest()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->authenticate(),
                'Accept'        => 'application/json',
            ],
            'json' => [
                'ShortCode'     => $this->shortCode,
                'CommandID'     => 'CustomerPayBillOnline',
                'Amount'        => $this->chargeAmount,
                'Msisdn'        => $this->safaricomNumber,
                'BillRefNumber' => $this->billReferenceNumber,
            ],
        ]);

        $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_C2B_SIMULATE_URL));

        return $response;
    }
}
