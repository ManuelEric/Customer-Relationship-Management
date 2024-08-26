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
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->date('refund_date')->nullable()->after('empl_id');
            $table->date('failed_date')->nullable()->after('empl_id');
            $table->date('success_date')->nullable()->after('empl_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->dropColumn('refund_date');
            $table->dropColumn('failed_date');
            $table->dropColumn('success_date');
        });
    }
};
