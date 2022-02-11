<?php

namespace Mjedari\MellatPay\Services;

use Mjedari\MellatPay\Gateway;
use Mjedari\MellatPay\Models\Transaction;

class TransactionService extends Gateway
{
    /**
     * Transaction instance
     */
    protected $transaction;

    /**
     * Transaction success closure
     */
    protected $thenCallback;

    /**
     * Transaction failed closure
     */
    protected $catchCallback;

    /**
     * Transaction service initiation
     */
    public function __construct(Transaction $transaction)
    {
        parent::__construct();
        $this->transaction = $transaction;
    }

    /**
     * Transaction pay request
     */
    public function pay()
    {
        $data = [
            'orderId' => $this->transaction->id,
            'price' => $this->transaction->price,
            'localDate' => now()->format('Ymd'),
            'localTime' => now()->format('His'),
            'callBackUrl' => $this->callback ?? config('mellatpay.callback'),
            'payerId' => $this->transaction->payerId,
            'description' => $this->transaction->description
        ];

        return Gateway::bpPayRequest($data);
    }

    /**
     * Transaction verify request
     */
    public function verify()
    {
        $data = [
            'orderId' => $this->transaction->id,
            'saleOrderId' => $this->transaction->id,
            'saleReferenceId' => $this->transaction->tracking_code,
        ];

        return Gateway::bpVerifyRequest($data);
    }

    /**
     * Transaction settle request
     */
    public function settle()
    {
        $data = [
            'orderId' => $this->transaction->id,
            'saleOrderId' => $this->transaction->id,
            'saleReferenceId' => $this->transaction->tracking_code,
        ];

        return Gateway::bpSettleRequest($data);
    }

    /**
     * Transaction inquiry request
     */
    public function inquiry()
    {
        $data = [
            'orderId' => $this->transaction->id,
            'saleOrderId' => $this->transaction->id,
            'saleReferenceId' => $this->transaction->tracking_code,
        ];

        return Gateway::bpInquiryRequest($data);
    }

    /**
     * Transaction refund request
     * needs :
     */
    public function refund()
    {
        $data = [
            'orderId' => $this->transaction->id,
            'saleOrderId' => $this->transaction->id,
            'saleReferenceId' => $this->transaction->tracking_code,
            'refundAmount' => $this->transaction->price
        ];

        return Gateway::bpRefundRequest($data);
    }

    /**
     * Transaction reverse request
     * needs verify. it works only after verifying transaction
     */
    public function reverse()
    {
        $data = [
            'orderId' => $this->transaction->id,
            'saleOrderId' => $this->transaction->id,
            'saleReferenceId' => $this->transaction->tracking_code,
        ];

        return Gateway::bpReversalRequest($data);
    }

    /**
     * Sets transaction success closure
     */
    public function then(callable $func)
    {
        $this->thenCallback = $func;
        return $this;
    }

    /**
     * Sets transaction then closure
     */
    public function catch(callable $func)
    {
        $this->catchCallback = $func;
        return $this->verify();
    }
}
