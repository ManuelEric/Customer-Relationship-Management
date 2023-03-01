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
            $table->renameColumn('menus_id', 'id');
            $table->renameColumn('menus_menu', 'submenu_name');
            $table->renameColumn('menus_link', 'submenu_link');
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
            //
        });
    }
};
