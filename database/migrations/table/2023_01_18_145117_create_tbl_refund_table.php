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
        Schema::create('tbl_refund', function (Blueprint $table) {
            $table->id();
            $table->char('invb2b_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('invb2b_id')->references('invb2b_id')->on('tbl_invb2b')->onUpdate('cascade')->onDelete('cascade');
            
            $table->char('inv_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('inv_id')->references('inv_id')->on('tbl_inv')->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('invdtl_id')->nullable();
            $table->foreign('invdtl_id')->references('invdtl_id')->on('tbl_invdtl')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('total_payment');
            $table->double('percentage_payment');
            $table->integer('refunded_amount');
            $table->integer('refunded_tax_amount');
            $table->double('refunded_tax_percentage');
            $table->integer('total_refunded');
            $table->boolean('status');

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
        Schema::dropIfExists('tbl_refund');
    }
};
