<?php

namespace Mjedari\MellatPay\Exceptions;

use Exception;

class InvalidGatewayOrigin extends Exception
{
    protected $message = "Gateway origin is not valid!";

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
