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
            $table->boolean('download_idr')->after('receipt_status')->default(false);
            $table->boolean('download')->after('receipt_status')->default(false);
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
            $table->dropColumn('download_idr');
            $table->dropColumn('download');
        });
    }
};
