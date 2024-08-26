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
            $table->enum('registration_type', ['FE'])->comment('FE : Form Embed')->nullable()->after('session_tutor');
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
            $table->dropColumn('registration_type');
        });
    }
};
