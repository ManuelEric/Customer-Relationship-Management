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
        Schema::table('tbl_sch_prog', function (Blueprint $table) {
            $table->integer('status')->comment('0: Pending, 1: Success, 2: Denied', '3: Refund')->change();
            // DB::statement("ALTER TABLE tbl_sch_prog MODIFY COLUMN status COMMENT '0: Pending, 1: Success, 2: Denied, 3: Refund'");
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
