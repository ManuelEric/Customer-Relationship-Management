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
        Schema::table('tbl_refund', function (Blueprint $table) {
            $table->renameColumn('refunded_amount', 'total_paid');
            $table->renameColumn('refunded_tax_amount', 'tax_amount');
            $table->renameColumn('refunded_tax_percentage', 'tax_percentage');
            $table->renameColumn('percentage_payment', 'percentage_paid');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_refund', function (Blueprint $table) {
            //
        });
    }
};
