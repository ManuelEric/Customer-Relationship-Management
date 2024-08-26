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
        Schema::create('tbl_invdtl', function (Blueprint $table) {
            $table->id('invdtl_id');

            $table->char('invb2b_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('invb2b_id')->references('invb2b_id')->on('tbl_invb2b')->onUpdate('cascade')->onDelete('cascade');
            
            $table->char('inv_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('inv_id')->references('inv_id')->on('tbl_inv')->onUpdate('cascade')->onDelete('cascade');
            
            $table->string('invdtl_installment')->nullable();
            $table->date('invdtl_duedate')->nullable();
            $table->float('invdtl_percentage')->nullable();
            $table->integer('invdtl_amount')->nullable();
            $table->integer('invdtl_amountidr')->nullable();
            $table->tinyInteger('invdtl_status')->default('0');
            $table->bigInteger('invdtl_cursrate')->nullable();
            $table->enum('invdtl_currency', ['gbp', 'usd', 'sgd', 'idr'])->nullable();

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
        Schema::dropIfExists('tbl_invdtl');
    }
};
