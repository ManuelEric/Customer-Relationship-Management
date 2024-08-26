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
            $table->renameColumn('percentage_paid', 'percentage_refund');
            $table->integer('refund_amount')->after('total_payment');
            $table->dropColumn('total_paid');
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
