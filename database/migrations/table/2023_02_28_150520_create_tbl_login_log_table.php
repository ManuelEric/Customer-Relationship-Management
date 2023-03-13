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
        Schema::create('tbl_login_log', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_type_id');
            $table->foreign('user_type_id')->references('id')->on('tbl_user_type_detail')->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('status')->comment('0: logout, 1: login');

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
        Schema::dropIfExists('tbl_login_log');
    }
};
