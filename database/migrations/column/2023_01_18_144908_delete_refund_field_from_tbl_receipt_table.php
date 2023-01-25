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
            $table->dropColumn('refund_total_payment');
            $table->dropColumn('refund_percentage_payment');
            $table->dropColumn('refund_amount');
            $table->dropColumn('refund_tax_percentage');
            $table->dropColumn('refund_tax_amount');
            $table->dropColumn('total_refunded');
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
