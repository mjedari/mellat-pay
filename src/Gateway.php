<?php

namespace Mjedari\MellatPay;

use Illuminate\Support\Facades\Log;
use Mjedari\MellatPay\Exceptions\GatewayException;
use SoapClient;
use SoapFault;

class Gateway
{
    /**
     * Gateway credentials
     */
    protected $credentials;

    /**
     * SOAP client
     */
    protected $client;

    /**
     * Initialize gateway
     */
    public function __construct()
    {
        $this->credentials = $this->initCredentials();
        $this->client = new SoapClient(config('mellatpay.server'), [
            'encoding' => 'UTF-8',
            'trace' => 1,
            'exceptions' => 1,
            'connection_timeout' => config('mellatpay.timeout') ?? 60,
        ]);
    }

    /**
     * Gateway pay request
     *
     * @param string $orderId
     * @param string $amount
     * @param string $localDate
     * @param string $localTime
     * @param string $callBackUrl
     * @param string $payerId
     * @param string $additionalData
     * @return Mjedari\MellatPay\GatewayResponse
     */
    protected function bpPayRequest($data)
    {
        $params = array_merge($this->credentials, $data);

        try {
            $response = $this->client->bpPayRequest($params);
            return GatewayResponse::response($response);

            Log::emergency("bpPayRequest return status is not 0");
        } catch (\SoapFault $e) {
            Log::emergency("SoapFault in bpPayRequest request: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Gateway verify request
     *
     * @param string $orderId
     * @param string $saleOrderId
     * @param string $saleReferenceId
     * @return boolean
     */
    protected function bpVerifyRequest($data)
    {
        $params = array_merge($this->credentials, $data);

        try {
            $response = $this->client->bpVerifyRequest($params);
            return GatewayResponse::response($response);

            Log::emergency("VerifyPayment failed with code: ".$response->return);
        } catch (\SoapFault $e) {
            Log::emergency("VerifyPayment failed with message: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * Gateway settle request
     *
     * @param string $orderId
     * @param string $saleOrderId
     * @param string $saleReferenceId
     * @return boolean
     */
    protected function bpSettleRequest($data)
    {
        $params = array_merge($this->credentials, $data);

        try {
            $response = $this->client->bpSettleRequest($params);

            // if ($response->return == '0' || $response->return == '45') {
            //     return true;
            // }
            return GatewayResponse::response($response);
        } catch (\SoapFault $e) {
            Log::emergency("Error in client soap request: ".$e->getMessage());
            throw $e;
        }
    }

    /**
    * Gateway inquiry request
    *
    * @param string $orderId
    * @param string $saleOrderId
    * @param string $saleReferenceId
    * @return string
    */
    protected function bpInquiryRequest($data)
    {
        $params = array_merge($this->credentials, $data);

        try {
            $response = $this->client->bpInquiryRequest($params);
            return GatewayResponse::response($response);
        } catch (\SoapFault $e) {
            Log::emergency("Error in client soap request: ".$e->getMessage());
            throw $e;
        }
    }

    protected function bpReversalRequest($data)
    {
        $params = array_merge($this->credentials, $data);

        try {
            $response = $this->client->bpReversalRequest($params);
            return GatewayResponse::response($response);
        } catch (\SoapFault $e) {
            Log::emergency("Error in client soap request: ".$e->getMessage());
            throw $e;
        }
    }

    protected function bpRefundRequest($data)
    {
        $params = array_merge($this->credentials, $data);

        try {
            $response = $this->client->bpRefundRequest($params);
            return GatewayResponse::response($response);
        } catch (\SoapFault $e) {
            Log::emergency("Error in client soap request: ".$e->getMessage());
            throw $e;
        }
    }

    protected function initCredentials()
    {
        return [
            'terminalId' => config('mellatpay.credentials.terminalId'),
            'userName' => config('mellatpay.credentials.username'),
            'userPassword' => config('mellatpay.credentials.password'),
        ];
    }
}
