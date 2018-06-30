<?php

namespace Imarishwa\MpesaBridge\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class TransactionStatus extends BaseDriver
{
    protected $initiatorName;
    protected $initiatorShortCode;
    protected $initiatorPassword;
    protected $transactionID;
    protected $partyA;
    protected $identifierType;
    protected $occasion;
    protected $remarks;
    protected $queueTimeOutURL;
    protected $resultURL;
    protected $initiatorSecurityCredential;

    public function using($initiatorName, $initiatorShortCode, $initiatorPassword, $initiatorSecurityCredential)
    {
        $this->initiatorName = $initiatorName;
        $this->initiatorShortCode = $initiatorShortCode;
        $this->initiatorPassword = $initiatorPassword;
        $this->initiatorSecurityCredential = $initiatorSecurityCredential;
        $this->identifierType = 4;
        $this->partyA = $this->initiatorShortCode;

        return $this;
    }

    public function transactionID(string $transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    public function partyA($partyA)
    {
        if ((strlen($partyA) === 6)) {
            $this->partyA = $partyA;
            $this->identifierType = 4;
        } elseif (strlen($partyA) === 12) {
            $this->partyA = $partyA;
            $this->identifierType = 1;
        } else {
            throw new \InvalidArgumentException('Party A must either be a valid shortcode or an MSISDN');
        }

        return $this;
    }

    public function occasion(string $occasion)
    {
        $this->occasion = $occasion;

        return $this;
    }

    public function remarks(string $remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function queueTimeOutURL($timeoutURL)
    {
        $this->queueTimeOutURL = $timeoutURL;

        return $this;
    }

    public function resultURL($resultURL)
    {
        $this->resultURL = $resultURL;

        return $this;
    }

    public function paramsValid()
    {
        if ((is_null($this->initiatorName) ||
            is_null($this->transactionID) ||
            is_null($this->partyA) ||
            is_null($this->identifierType) ||
            is_null($this->queueTimeOutURL) ||
            is_null($this->resultURL))) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     * @throws \Imarishwa\MpesaBridge\Exceptions\MpesaRequestException
     *
     * @return mixed|null
     */
    public function checkStatus()
    {
        if (stringNullOrEmpty($this->resultURL)) {
            if (stringNotNullOrEmpty($this->config['check_transaction_result_url'])) {
                $this->resultURL = $this->config['check_transaction_result_url'];
            } else {
                throw new \InvalidArgumentException('result url is mandatory');
            }
        }

        if (stringNullOrEmpty($this->queueTimeOutURL)) {
            if (stringNotNullOrEmpty($this->config['queue_timeout_url'])) {
                $this->queueTimeOutURL = $this->config['queue_timeout_url'];
            } else {
                throw new \InvalidArgumentException('Queue timeout url is mandatory');
            }
        }

        if (stringNullOrEmpty($this->initiatorName) || stringNullOrEmpty($this->initiatorShortCode) || stringNullOrEmpty($this->initiatorPassword)) {
            if (stringNotNullOrEmpty($this->config['default_initiator_name'])) {
                $this->initiatorName = $this->config['default_initiator_name'];
            } else {
                throw new \InvalidArgumentException('initiator name is mandatory');
            }
            if (stringNotNullOrEmpty($this->config['default_initiator_short_code'])) {
                $this->initiatorShortCode = $this->config['default_initiator_short_code'];
            } else {
                throw new \InvalidArgumentException('initiator short code is mandatory');
            }
            if (stringNotNullOrEmpty($this->config['default_initiator_security_credential'])) {
                $this->initiatorPassword = $this->config['default_initiator_security_credential'];
            } else {
                throw new \InvalidArgumentException('initiator password is mandatory');
            }

            $this->identifierType = 4;
            $this->partyA = $this->initiatorShortCode;
        }

        if (stringNullOrEmpty($this->remarks)) {
            $this->remarks = 'Check transaction status';
        }

        if (!$this->paramsValid()) {
            throw new \InvalidArgumentException('resultURL, queueTimeOutURL, amount, transactionID, initiator fields may be missing');
        }

        try {
            $response = $this->buildRequest();

            return \json_decode($response->getBody(), true);
        } catch (RequestException $exception) {
            return json_decode($exception->getResponse()->getBody());
            $this->handleException($exception);
        }
    }

    /**
     * @throws \Imarishwa\MpesaBridge\Exceptions\MissingBaseApiDomainException
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function buildRequest()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->authenticate(),
                'Accept'        => 'application/json',
            ],
            'json' => [
                'CommandID'          => 'TransactionStatusQuery',
                'PartyA'             => $this->initiatorShortCode,
                'IdentifierType'     => $this->identifierType,
                'Remarks'            => $this->remarks,
                'Initiator'          => $this->initiatorName,
                'SecurityCredential' => $this->initiatorSecurityCredential,
                'QueueTimeOutURL'    => $this->queueTimeOutURL,
                'ResultURL'          => $this->resultURL,
                'TransactionID'      => $this->transactionID,
                'Occasion'           => $this->occasion,
            ],
        ]);

        $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_REVERSAL_URL));

        return $response;
    }
}
