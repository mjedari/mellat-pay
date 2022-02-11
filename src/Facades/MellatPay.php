<?php

namespace Mjedari\MellatPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Mjedari\MellatPay\MellatPay mobile($number)
 * @method static \Mjedari\MellatPay\MellatPay payable(Model $modal)
 * @method static \Mjedari\MellatPay\MellatPay price($price)
 * @method static \Mjedari\MellatPay\MellatPay callback($url)
 * @method static \Mjedari\MellatPay\MellatPay description($text)
 * @method static \Mjedari\MellatPay\MellatPay payer($id)
 * @method static \Mjedari\MellatPay\MellatPay ready()
 * @method static view redirect()
 * @method static boolean|\Mjedari\MellatPay\MellatPay verify()
 * @method static \Mjedari\MellatPay\Services\Transaction transaction()
 *
 * @see \Mjedari\MellatPay\MellatPay
 */
class MellatPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mellat-pay';
    }
}
