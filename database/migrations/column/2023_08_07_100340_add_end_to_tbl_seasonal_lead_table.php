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
        Schema::table('tbl_seasonal_lead', function (Blueprint $table) {
            $table->date('sales_date')->nullable()->after('start')->comment('The date when sales department can start selling');
            $table->date('end')->nullable()->after('start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_seasonal_lead', function (Blueprint $table) {
            $table->dropColumn('sales_date');
            $table->dropColumn('end');
        });
    }
};
