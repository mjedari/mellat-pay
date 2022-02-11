<?php

namespace Mjedari\MellatPay\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('mellatpay.table'.'_logd');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }



}
