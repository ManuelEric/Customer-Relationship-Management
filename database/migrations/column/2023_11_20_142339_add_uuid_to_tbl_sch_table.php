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
        Schema::table('tbl_sch', function (Blueprint $table) {
            $table->string('uuid', 39)->after('sch_id')->default(DB::raw('(CONCAT("vs-", UUID()))'))->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_sch', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
