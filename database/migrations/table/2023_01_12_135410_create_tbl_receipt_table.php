<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_receipt', function (Blueprint $table) {
            $table->id();
            $table->char('receipt_id', 50)->collation('utf8mb4_general_ci')->unique();
            $table->enum('receipt_cat', ['student', 'school']);
            $table->char('inv_id', 50)->collation('utf8mb4_general_ci');
            $table->foreign('inv_id')->references('inv_id')->on('tbl_inv')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('invdtl_id')->nullable();
            $table->foreign('invdtl_id')->references('invdtl_id')->on('tbl_invdtl')->onUpdate('cascade')->onDelete('cascade');
            $table->char('invb2b_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('invb2b_id')->references('invb2b_id')->on('tbl_invb2b')->onUpdate('cascade')->onDelete('cascade');

            $table->string('receipt_method')->nullable();
            $table->string('receipt_cheque', 50)->nullable();
            $table->integer('receipt_amount')->nullable();
            $table->text('receipt_words')->nullable();
            $table->integer('receipt_amount_idr')->nullable();
            $table->text('receipt_words_idr')->nullable();
            $table->text('receipt_notes')->nullable();
            $table->integer('receipt_status')->default('1')->comment('1: success, 2: refund');

            $table->bigInteger('refund_total_payment')->comment("total bayar")->nullable();
            $table->integer('refund_percentage_payment')->nullable();
            $table->bigInteger('refund_amount')->comment('nominal refund')->nullable();
            $table->integer('refund_tax_percentage')->nullable();
            $table->bigInteger('refund_tax_amount')->nullable();
            $table->bigInteger('total_refunded')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_receipt');
    }
};
