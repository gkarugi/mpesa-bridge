<?php

namespace Imarishwa\MpesaBridge\Drivers;

use GuzzleHttp\Client;
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
    protected $securityCredential;

    public function using($initiatorName, $initiatorShortCode, $initiatorPassword)
    {
        $this->initiatorName = $initiatorName;
        $this->initiatorShortCode = $initiatorShortCode;
        $this->initiatorPassword = $initiatorPassword;
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
        $this->partyA = $partyA;

        return $this;
    }

    public function identifierType($identifierType)
    {
        $this->identifierType = $identifierType;

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
            is_null($this->partyA) ||
            is_null($this->identifierType) ||
            is_null($this->queueTimeOutURL) ||
            is_null($this->resultURL)) {
            return false;
        }

        return true;
    }

    public function checkStatus()
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
            $this->remarks = 'Check transaction status';
        }

        if ($this->paramsValid()) {
            dd($this);

            return $this->buildRequest();
        } else {
            throw new \InvalidArgumentException('resultURL, queueTimeOutURL, amount, transactionID, initiator fields may be missing');
        }
    }

    public function buildRequest()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->authenticate(),
                'Accept'        => 'application/json',
            ],
            'json' => [
                'CommandID'          => 'TransactionStatusQuery',
                'PartyA'             => $this->partyA,
                'IdentifierType'     => $this->identifierType,
                'Remarks'            => $this->remarks,
                'Initiator'          => $this->initiatorName,
                'SecurityCredential' => $this->initiatorPassword,
                'QueueTimeOutURL'    => $this->queueTimeOutURL,
                'ResultURL'          => $this->resultURL,
                'TransactionID'      => $this->transactionID,
                'Occasion'           => $this->occasion,
            ],
        ]);

        try {
            $response = $client->send(new Request('POST', $this->getApiBaseUrl().MPESA_REVERSAL_URL));
            dd(\json_decode($response->getBody(), true));

            return \json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            dd(\json_decode($e->getResponse()->getBody()->getContents()));

            return \json_decode($e->getResponse()->getBody()->getContents());
        }
    }
}
