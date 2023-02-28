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
        Schema::table('tbl_receipt', function (Blueprint $table) {
            $table->boolean('download_idr')->default('0')->comment('0: Not Yet, 1: Downloaded')->after('receipt_status');
            $table->boolean('download_other')->default('0')->comment('0: Not Yet, 1: Downloaded')->after('receipt_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_receipt', function (Blueprint $table) {
            //
        });
    }
};
