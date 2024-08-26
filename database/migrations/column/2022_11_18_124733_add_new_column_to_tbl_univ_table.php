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
        Schema::table('tbl_univ', function (Blueprint $table) {
            $table->string('univ_phone')->nullable()->after('univ_country');
            $table->string('univ_email')->nullable()->after('univ_country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_univ', function (Blueprint $table) {
            //
        });
    }
};
