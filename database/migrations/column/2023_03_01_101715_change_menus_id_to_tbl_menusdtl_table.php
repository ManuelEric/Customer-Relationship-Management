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
        Schema::table('tbl_menusdtl', function (Blueprint $table) {
            $table->dropForeign(['menus_id']);
            $table->dropColumn('menus_id');

            $table->unsignedBigInteger('menu_id')->after('menusdtl_id');
            $table->foreign('menu_id')->references('id')->on('tbl_menus')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_menusdtl', function (Blueprint $table) {
            //
        });
    }
};
