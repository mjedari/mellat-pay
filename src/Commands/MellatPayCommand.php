<?php

namespace Mjedari\MellatPay\Commands;

use Illuminate\Console\Command;

class MellatPayCommand extends Command
{
    public $signature = 'mellat-pay';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
