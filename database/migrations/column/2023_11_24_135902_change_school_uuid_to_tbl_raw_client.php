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
        Schema::table('tbl_raw_client', function (Blueprint $table) {
            $table->dropColumn('school_uuid');
            $table->char('sch_id', 8)->collation('utf8mb4_general_ci')->after('relation_key')->nullable();
            $table->foreign('sch_id')->references('sch_id')->on('tbl_sch')->onUpdate('cascade')->onDelete('cascade');
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
