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
            $table->integer('status')->comment('0: Pending, 1: Success, 2: Rejected 3: Refund 4: Accepted 5: Cancel')->change();
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
