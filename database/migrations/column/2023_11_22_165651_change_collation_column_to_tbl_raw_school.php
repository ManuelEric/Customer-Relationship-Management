<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('tbl_raw_school', function (Blueprint $table) {
            $table->string('uuid', 39)->default(DB::raw('(CONCAT("rs-", UUID()))'))->change();
            DB::statement("ALTER TABLE tbl_raw_school CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_raw_school', function (Blueprint $table) {
            //
        });
    }
};
