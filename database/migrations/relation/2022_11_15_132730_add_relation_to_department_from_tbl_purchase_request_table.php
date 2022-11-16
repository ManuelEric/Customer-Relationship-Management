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
        Schema::table('tbl_purchase_request', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_department')->change();
            $table->foreign('purchase_department')->references('id')->on('tbl_department')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_purchase_request', function (Blueprint $table) {
            //
        });
    }
};
