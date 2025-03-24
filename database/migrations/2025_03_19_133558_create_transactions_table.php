<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigInteger('trx_id')->primary();
            $table->string('invoice_id')->nullable();
            $table->string('installment_id')->nullable();
            $table->string('invoice_number');
            $table->string('trx_currency');
            $table->string('trx_amount');
            $table->string('item_title');
            $table->string('payment_method');
            $table->string('bank_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('payment_page_url');
            $table->string('va_number')->nullable();
            $table->string('merchant_ref_no');
            $table->string('plink_ref_no');
            $table->timestamp('validity');
            $table->enum('payment_status', ['SETLD', 'REJEC', 'PNDNG'])->comment('settled, rejected, pending')->default('PNDNG');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
