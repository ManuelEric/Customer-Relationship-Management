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
        Schema::table('tbl_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('mainmenu_id')->after('id');
            $table->foreign('mainmenu_id')->references('id')->on('tbl_main_menus')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('order_no')->after('submenu_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_menus', function (Blueprint $table) {
            $table->dropForeign('mainmenu_id');
            $table->dropColumn('mainmenu_id');
            $table->dropColumn('order_no');
        });
    }
};
