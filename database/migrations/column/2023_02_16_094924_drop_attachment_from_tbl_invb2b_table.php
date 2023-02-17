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
        Schema::table('tbl_invb2b', function (Blueprint $table) {
            $table->dropColumn('approve_date');
            $table->dropColumn('sign_status');
            $table->dropColumn('attachment');
            $table->dropColumn('send_to_client');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_invb2b', function (Blueprint $table) {
            //
        });
    }
};
