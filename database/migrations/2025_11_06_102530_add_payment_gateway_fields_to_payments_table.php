<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentGatewayFieldsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Payment Gateway Fields
            $table->string('payment_type')->default('manual')->after('method');
            // Options: 'manual', 'gateway', 'crypto'

            $table->string('gateway_name')->nullable()->after('payment_type');
            // e.g., 'midtrans', 'xendit', 'coinbase'

            $table->string('transaction_id')->nullable()->after('gateway_name');
            // Transaction ID from payment gateway

            $table->text('payment_url')->nullable()->after('transaction_id');
            // URL untuk redirect customer ke payment page

            $table->text('gateway_response')->nullable()->after('payment_url');
            // Raw response dari gateway (JSON)

            // Crypto Payment Fields
            $table->string('crypto_currency')->nullable()->after('gateway_response');
            // e.g., 'BTC', 'ETH', 'USDT'

            $table->decimal('crypto_amount', 20, 8)->nullable()->after('crypto_currency');
            // Amount dalam crypto

            $table->string('crypto_address')->nullable()->after('crypto_amount');
            // Wallet address untuk menerima payment

            $table->string('crypto_tx_hash')->nullable()->after('crypto_address');
            // Transaction hash di blockchain

            $table->timestamp('expires_at')->nullable()->after('paid_at');
            // Payment expiration time
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'gateway_name',
                'transaction_id',
                'payment_url',
                'gateway_response',
                'crypto_currency',
                'crypto_amount',
                'crypto_address',
                'crypto_tx_hash',
                'expires_at',
            ]);
        });
    }
}
