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
        Schema::create('tbl_menusdtl', function (Blueprint $table) {
            $table->id('menusdtl_id');

            $table->unsignedBigInteger('menus_id');
            $table->foreign('menus_id')->references('menus_id')->on('tbl_menus')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('tbl_department')->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('menus_mainmenu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_menusdtl');
    }
};
