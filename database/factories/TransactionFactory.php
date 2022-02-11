<?php

namespace Mjedari\MellatPay\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mjedari\MellatPay\Models\Transaction;
use Mjedari\MellatPay\Enums\Status;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'payer_id' => '1',
            'payable_type' => 'App\Models\User',
            'payable_id' => '1',
            'price' => '10000',
            'ref_id' => '',
            'tracking_code' => '',
            'card_number' => '',
            'status' => $this->faker->randomElement([
                Status::INIT,
                Status::SUCCEED,
                Status::FAILED,
                Status::PENDING,
                Status::VERIFY_PENDING,
                Status::SETTLE_PENDING,
                Status::CANCELED,
                Status::REFUNDED,
                Status::REVERSED,
            ]),

            'ip' => $this->faker->ipv4(),
            'description' => $this->faker->sentence(),
            'paid_at' => '',
            'deleted_at' => ''
        ];
    }
}
