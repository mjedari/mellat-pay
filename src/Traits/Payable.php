<?php

namespace Mjedari\MellatPay\Traits;

trait Payable
{

    /**
     * Models transaction relation
     */
    public function transactions()
    {
        return $this->morphMany(\Mjedari\MellatPay\Models\Transaction::class, 'payable');
    }
}
