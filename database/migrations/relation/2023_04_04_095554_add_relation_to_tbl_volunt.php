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
        Schema::table('tbl_volunt', function (Blueprint $table) {
            $table->char('volunt_graduatedfr', 8)->collation('utf8mb4_general_ci')->after('volunt_phone')->nullable();
            $table->foreign('volunt_graduatedfr')->references('univ_id')->on('tbl_univ')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('volunt_major')->after('volunt_phone')->nullable();
            $table->foreign('volunt_major')->references('id')->on('tbl_major')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('volunt_position')->after('volunt_phone')->nullable();
            $table->foreign('volunt_position')->references('id')->on('tbl_position')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_volunt', function (Blueprint $table) {
            //
        });
    }
};
