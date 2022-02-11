<?php

namespace Mjedari\MellatPay\Enums;

class Status
{
    public const INIT = 'INIT';
    public const SUCCEED = 'SUCCEED';
    public const FAILED = 'FAILED';
    public const PENDING = 'PENDING';
    public const VERIFY_PENDING = 'VERIFY_PENDING';
    public const SETTLE_PENDING = 'SETTLE_PENDING';
    public const CANCELED = 'CANCELED';
    public const REFUNDED = 'REFUNDED';
    public const REVERSED = 'REVERSED';

}
