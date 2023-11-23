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
        Schema::table('tbl_raw_client', function (Blueprint $table) {
            $table->string('uuid', 36)->change();
            DB::statement("ALTER TABLE tbl_raw_client MODIFY school_uuid VARCHAR(39) CHARACTER SET utf8 COLLATE utf8_general_ci");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_raw_client', function (Blueprint $table) {
            //
        });
    }
};
