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
        Schema::table('tbl_client_lead_tracking', function (Blueprint $table) {
            $table->double('potential_point')->default(0)->after('total_result')->comment('this point is for digital team tracker');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_lead_tracking', function (Blueprint $table) {
            $table->dropColumn('potential_point');
        });
    }
};
