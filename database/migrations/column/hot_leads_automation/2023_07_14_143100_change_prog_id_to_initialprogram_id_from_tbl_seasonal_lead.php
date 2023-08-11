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
        Schema::table('tbl_seasonal_lead', function (Blueprint $table) {
            $table->dropForeign(['prog_id']);
            $table->dropColumn('prog_id');

            $table->unsignedBigInteger('initialprogram_id')->after('id');
            $table->foreign('initialprogram_id')->references('id')->on('tbl_initial_program_lead')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_seasonal_lead', function (Blueprint $table) {
            //
        });
    }
};
