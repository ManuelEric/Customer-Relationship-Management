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
        Schema::create('tbl_purchase_dtl', function (Blueprint $table) {
            $table->id();
            $table->char('purchase_id')->collation('utf8mb4_general_ci');
            $table->foreign('purchase_id')->references('purchase_id')->on('tbl_purchase_request')->onUpdate('cascade')->onDelete('cascade');

            $table->string('item');
            $table->integer('amount');
            $table->integer('price_per_unit');
            $table->string('notes')->nullable();
            $table->integer('total');
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
        Schema::dropIfExists('tbl_purchase_dtl');
    }
};
