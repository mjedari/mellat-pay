<?php

namespace Mjedari\MellatPay\Exceptions;

use Exception;

class NotFoundTransaction extends Exception
{
    protected $message = "Transaction Not found!";
    
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }
}
