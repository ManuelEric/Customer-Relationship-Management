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
        Schema::table('tbl_prog', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_prog_id')->after('prog_id')->nullable();
            $table->foreign('sub_prog_id')->references('id')->on('tbl_sub_prog')->onUpdate('restrict')->onDelete('restrict');

            $table->unsignedBigInteger('main_prog_id')->after('prog_id')->nullable();
            $table->foreign('main_prog_id')->references('id')->on('tbl_main_prog')->onUpdate('restrict')->onDelete('restrict');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_prog', function (Blueprint $table) {
            $table->dropColumn('sub_prog_id');
            $table->dropColumn('main_prog_id');
        });
    }
};
