<?php

namespace Mjedari\MellatPay\Models;

use Illuminate\Database\Eloquent\Model;
use Mjedari\MellatPay\Enums\Status;

class Transaction extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        // set generated time base id
        static::creating(function ($model) {
            $model->id = substr(str_pad(str_replace('.', '', microtime(true)), 12, 0), 0, 12);
        });
    }

    public function getTable()
    {
        return config('mellatpay.table');
    }

    public function getStatusTextAttribute()
    {
        if (!$this->status) {
            return "No Status";
        }

        return  __("mellat-pay::messages.status.".strtolower($this->status));
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function log()
    {
        return $this->hasMany(TransactionLog::class);
    }
}
