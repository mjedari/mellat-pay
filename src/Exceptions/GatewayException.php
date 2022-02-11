<?php

namespace Mjedari\MellatPay\Exceptions;

use Exception;

/**
 * @method static GatewayException code()
 */
class GatewayException extends Exception
{
    public static function code($code): self
    {
        return new static(trans("mellat-pay::exceptions.{$code}"), $code);
    }
}
