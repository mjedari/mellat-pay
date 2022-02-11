<?php

namespace Mjedari\MellatPay\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Mjedari\MellatPay\Models\Transaction;

class TransactionConfirmed
{
    use Dispatchable, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
