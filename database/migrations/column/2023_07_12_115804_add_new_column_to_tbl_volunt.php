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
            $table->char('univ_id', 8)->collation('utf8mb4_general_ci')->nullable()->after('volunt_lasteditdate');
            $table->foreign('univ_id')->references('univ_id')->on('tbl_univ')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('major_id')->nullable()->after('volunt_lasteditdate');
            $table->foreign('major_id')->references('id')->on('tbl_major')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('position_id')->nullable()->after('volunt_lasteditdate');
            $table->foreign('position_id')->references('id')->on('tbl_position')->onUpdate('cascade')->onDelete('cascade');
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
