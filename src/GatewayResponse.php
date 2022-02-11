<?php

namespace Mjedari\MellatPay;

use Mjedari\MellatPay\Exceptions\GatewayException;

class GatewayResponse
{
    /**
     * Gateway return code
     */
    public $code;

    /**
     * Gateway message
     */
    public $message;

    /**
     * Gateway pay request reference id
     */
    public $refId;

    /**
     * Request successful codes
     */
    protected $codes = [
        0 => 'تراکنش با موقیت انجام شد',
    ];

    /**
     * Populate gateway successful response
     */
    public function __construct($code, $refId = null)
    {
        $this->code = $code;
        $this->refId = $refId;
        $this->message = isset($this->codes[$this->code]) ? $this->codes[$this->code] : "Not Found!";
    }

    /**
     * Pars gateway raw response
     */
    public static function response($response)
    {
        $response = explode(',', $response->return);

        if ($response[0] != 0) {
            return throw GatewayException::code($response[0]);
        }

        return new GatewayResponse(...$response);
    }
}
