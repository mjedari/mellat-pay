<?php

namespace Mjedari\MellatPay;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Mjedari\MellatPay\Enums\Status;
use Mjedari\MellatPay\Events\TransactionConfirmed;
use Mjedari\MellatPay\Exceptions\GatewayException;
use Mjedari\MellatPay\Exceptions\InvalidGatewayOrigin;
use Mjedari\MellatPay\Exceptions\NotFoundTransaction;
use Mjedari\MellatPay\Models\Transaction;
use Mjedari\MellatPay\Models\TransactionLog;
use Mjedari\MellatPay\Services\TransactionService;

class MellatPay extends Gateway
{
    /**
     * Transaction instance
     */
    protected $transaction;

    /**
     * Mobile to pass bank gateway
     */
    protected $mobile;

    /**
     * User that pays
     */
    protected $payerId;

    /**
     * Amount of price in IRR format
     */
    protected $price;

    /***
     * Callback for each transaction
     */
    protected $callback;

    /**
     * Description to send bank
     */
    protected $description;

    /**
     * Transaction Id
     */
    protected $transactionId;

    /**
     * Transaction reference id from bank
     */
    protected $refId;

    /**
     * Transaction tracking code
     */
    protected $trackingCode;

    /**
     * Transaction card number
     */
    protected $cardNumber;

    /**
     * Morph to model instance
     */
    protected $payable;

    /**
     * Success closure
     */
    protected $thenClosure;

    /**
     * Failed closure
     */
    protected $catchClosure;

    /**
     * Sets payable model instance
     *
    * @param integer
    * @return MellatPay
    */
    public function payable(Model $payable)
    {
        $this->payable = $payable;
        return $this;
    }

    /**
     * Sets user mobile number
     *
     * @param string
     * @return MellatPay
     */
    public function mobile(string $number)
    {
        $this->mobile = $number;
        return $this;
    }

    /**
     * Sets transaction user id
     *
     * @param integer
     * @return MellatPay
     */
    public function payer(int $id)
    {
        $this->payerId = $id;
        return $this;
    }

    /**
     * Sets transaction price
     *
     * @param string
     * @return MellatPay
     */
    public function price(string $price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Sets transaction callback
     *
     * @param string
     * @return MellatPay
     */
    public function callback(string $url)
    {
        $this->callback = $url;
        return $this;
    }

    /**
     * Sets transaction description
     *
     * @param string
     * @return MellatPay
     */
    public function description(string $text)
    {
        $this->description = $text;
        return $this;
    }

    /**
     * Initiate transaction and make ready to redirect
     *
     * @return MellatPay
     */
    public function ready()
    {
        $this->initTransaction();

        $this->sendPayRequest();
        return $this;
    }

    /**
     * Redirect request to gateway
     *
     * @return Illuminate\Support\Facades\View
     */
    public function redirect()
    {
        $data = [
            'refId' => $this->refId,
            'mobile' => $this->mobile,
            'transaction' => $this->transaction
        ];

        $with = array_merge($this->with, $data);

        return View::make('mellat-pay::redirect')->with($with);
    }

    /**
     * Fill variable to send redirect page
     *
     * @param array $variables
     * @return MellatPay
     */
    public function with($variables)
    {
        $this->with = $variables;
        return $this;
    }

    public function test()
    {
        //
    }

    /**
     * Check and verify bank callback request and settle transaction
     * method try/catch
     *
     * @return MellatPay
     */
    public function verify()
    {
        $this->validateCallbackOrigin();

        $this->updateTransaction();
        $this->verifyPayment();
        $this->settlePayment();

        return $this;
    }

    /**
    * Check and verify bank callback request and settle transaction
    * method promise like
    *
    * @return MellatPay
    */
    public function confirm()
    {
        // $this->validateCallbackOrigin();

        if (empty($this->thenClosure)) {
            return $this;
        }

        try {
            $this->updateTransaction();
            $this->verifyPayment();
            $this->settlePayment();
            // throw new NotFoundTransaction;

            return ($this->thenClosure)($this);
        } catch (\Exception $e) {
            return ($this->catchClosure)($e);
            return $e;
        }
    }

    /**
     * Sets then success transaction closure
     *
     * @return MellatPay
     */
    public function then(Closure $closure)
    {
        $this->thenClosure = $closure;
        return $this;
    }

    /**
     * Sets then failure transaction closure
     *
     * @return
     */
    public function catch(Closure $closure)
    {
        $this->catchClosure = $closure;
        return $this->confirm();
    }

    /**
     * Transaction actions
     *
     * @param string|int|Mjedari\MellatPay\Models\Transaction $transaction
     * @return Mjedari\MellatPay\Services\TransactionService
     */
    public function transaction($transaction)
    {
        if (!($transaction instanceof Transaction)) {
            $transaction = Transaction::find($transaction);
        }

        if (!$transaction) {
            throw new NotFoundTransaction();
        }

        return new TransactionService($transaction);
    }

    protected function sendPayRequest()
    {
        $response = $this->transaction($this->transaction)->pay();

        if (!$response->refId) {
            // null received from bank
            return false;
        }

        $this->refId = $response->refId;

        // persist data
        $status = Transaction::whereId($this->transaction->id)->update([
            'ref_id' => $response->refId,
            'updated_at' => now()
        ]);

        return $status;
    }

    protected function updateTransaction()
    {
        $refId = request()->get('RefId');
        $responseCode = request()->get('ResCode');

        if ($responseCode == '0') {
            $status = Status::VERIFY_PENDING;
        }
        if ($responseCode == '17') {
            $status = Status::CANCELED;
        }

        // TODO: store in transaction table

        $transaction = Transaction::where('ref_id', $refId);

        $transaction->update([
            'status' => $status,
            'card_number' =>  request()->get("CardHolderPan") ,
            'tracking_code' => request()->get("SaleReferenceId"),
            'updated_at' => now()
        ]);


        if ($responseCode != '0') {
            throw GatewayException::code($responseCode);
        }

        $this->transaction = $transaction->get()->first();
    }


    protected function verifyPayment()
    {
        $this->transaction->refresh();

        $verified = $this->transaction($this->transaction)->verify();

        if ($verified) {
            // verified
        }

        // not verified
    }

    protected function settlePayment()
    {
        $settled = $this->transaction($this->transaction)->settle();

        if ($settled) {
            return $this->transactionSucceed();
        }
        return $this->transactionFailed();
    }

    /**
     * Initialize a record on db
     *
     * @return string
     */
    protected function initTransaction()
    {
        $data = [
            'payer_id' 	=> $this->payerId,
            'price' 		=> $this->price,
            'status' 		=> Status::INIT,
            'ip' 			=> Request::ip(),
            'description'	=> $this->description,
            'created_at' 	=> now(),
            'updated_at' 	=> now(),
        ];

        $this->transaction = $this->payable->transactions()->create($data);
        return $this->transaction;
    }

    protected function transactionSucceed()
    {
        $updateFields = [
            'status' => Status::SUCCEED,
            'paid_at' => now(),
            'updated_at' => now(),
        ];

        return Transaction::whereId($this->transaction->id)->update($updateFields);
    }

    protected function transactionFailed()
    {
        return Transaction::whereId($this->transaction->id)->update([
            'status' => Status::FAILED,
            'updated_at' => now(),
        ]);
    }

    /**
     * Validate bank callback origin host
     *
     */
    protected function validateCallbackOrigin()
    {
        if (request()->headers->get('origin') != config('mellatpay.origin')) {
            throw new InvalidGatewayOrigin;
        }
    }
}
