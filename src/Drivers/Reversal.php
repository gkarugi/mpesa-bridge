<?php

namespace Imarishwa\MpesaBridge\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Reversal extends BaseDriver
{
    protected $initiatorName;
    protected $initiatorShortCode;
    protected $initiatorPassword;
    protected $transactionID;
    protected $receiverParty;
    protected $receiverIdentifierType;
    protected $amount;
    protected $occasion;
    protected $remarks;
    protected $queueTimeOutURL;
    protected $resultURL;
    protected $securityCredential;

    public function using($initiatorName, $initiatorShortCode, $initiatorPassword)
    {
        $this->initiatorName = $initiatorName;
        $this->initiatorShortCode = $initiatorShortCode;
        $this->initiatorPassword = $initiatorPassword;
        $this->receiverIdentifierType = 4;
        $this->receiverParty = $this->initiatorShortCode;

        return $this;
    }

    public function transactionID(string $transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    public function receiverParty($receiverParty)
    {
        $this->receiverParty = $receiverParty;

        return $this;
    }

    public function receiverIdentifierType($receiverType)
    {
        $this->receiverIdentifierType = $receiverType;

        return $this;
    }

    public function amount($amount)
    {
        $this->amount = $amount;

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
        if (is_null($this->initiatorName) ||
            is_null($this->transactionID) ||
            is_null($this->receiverParty) ||
            is_null($this->receiverIdentifierType) ||
            is_null($this->amount) ||
            is_null($this->queueTimeOutURL) ||
            is_null($this->resultURL)) {
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
    public function reverse()
    {
        if (stringNullOrEmpty($this->resultURL)) {
            if (stringNotNullOrEmpty($this->config['reversal_result_url'])) {
                $this->resultURL = $this->config['reversal_result_url'];
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
            if (stringNotNullOrEmpty($this->config['default_initiator_password'])) {
                $this->initiatorPassword = $this->config['default_initiator_password'];
            } else {
                throw new \InvalidArgumentException('initiator password is mandatory');
            }

            $this->receiverIdentifierType = 4;
            $this->receiverParty = $this->initiatorShortCode;
        }

        if (stringNullOrEmpty($this->remarks)) {
            $this->remarks = 'Transaction reversal request';
        }

        if (!$this->paramsValid()) {
            throw new \InvalidArgumentException('resultURL, queueTimeOutURL, amount, transactionID, initiator fields may be missing');
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
     * @throws MissingBaseApiDomainException
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
                'CommandID'             => 'TransactionReversal',
                'Amount'                => $this->amount,
                'ReceiverParty'         => $this->receiverParty,
                'RecieverIdentifierType'=> $this->receiverIdentifierType,
                'Remarks'               => $this->remarks,
                'Initiator'             => $this->initiatorName,
                'SecurityCredential'    => $this->initiatorPassword,
                'QueueTimeOutURL'       => $this->queueTimeOutURL,
                'ResultURL'             => $this->resultURL,
                'TransactionID'         => $this->transactionID,
                'Occasion'              => $this->occasion,
            ],
        ]);

        $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_REVERSAL_URL));

        return $response;
    }
}
