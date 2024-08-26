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
        Schema::table('tbl_sales_target', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_prog_id')->nullable()->after('prog_id');

            $table->foreign('sub_prog_id')->references('id')->on('tbl_sub_prog')->onUpdate('cascade')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_sales_target', function (Blueprint $table) {
            //
        });
    }
};
