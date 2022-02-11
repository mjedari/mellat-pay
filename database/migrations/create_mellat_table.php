<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mjedari\MellatPay\Enums\Status;

return new class extends Migration {
    public function getTable()
    {
        return config('config.table', 'mellat_transactions');
    }

    public function getLogTable()
    {
        return $this->getTable().'_logs';
    }

    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payer_id')->nullable();
            $table->nullableMorphs('payable');
            $table->decimal('price', 15, 2);
            $table->string('ref_id', 100)->nullable();
            $table->string('tracking_code', 50)->nullable();
            $table->string('card_number', 50)->nullable();
            $table->enum('status', [
                Status::INIT,
                Status::SUCCEED,
                Status::FAILED,
                Status::PENDING,
                Status::VERIFY_PENDING,
                Status::SETTLE_PENDING,
                Status::CANCELED,
                Status::REFUNDED,
                Status::REVERSED,
            ])->default(Status::INIT);
            $table->string('ip', 20)->nullable();
            $table->string('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });

        Schema::create($this->getLogTable(), function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id');
            $table->string('result_code', 10);
            $table->string('result_message', 255);
            $table->timestamp('created_at');

            $table
                ->foreign('transaction_id')
                ->references('id')
                ->on($this->getTable())
                ->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::drop($this->getLogTable());
        Schema::drop($this->getTable());
    }
};
