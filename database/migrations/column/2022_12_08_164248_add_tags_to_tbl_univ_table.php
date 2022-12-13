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
            $table->unsignedBigInteger('tag')->after('univ_name')->nullable();
            $table->foreign('tag')->references('id')->on('tbl_tag')->onUpdate('cascade')->onDelete('cascade');
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
