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
            $table->integer('session_tutor')->nullable()->after('trial_date')->comment('for academic tutor only');
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
            $table->dropColumn('session_tutor');
        });
    }
};
