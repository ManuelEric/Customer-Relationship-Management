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
        Schema::table('tbl_sch_prog', function (Blueprint $table) {
            $table->date('pending_date')->nullable()->after('success_date');
            $table->date('accepted_date')->nullable()->after('success_date');
            $table->date('cancel_date')->nullable()->after('success_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_sch_prog', function (Blueprint $table) {
            //
        });
    }
};
