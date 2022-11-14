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
        Schema::create('tbl_sub_prog', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('main_prog_id');
            $table->foreign('main_prog_id')->references('id')->on('tbl_main_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->string('sub_prog_name');
            $table->boolean('sub_prog_status')->default(True);
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
        Schema::dropIfExists('tbl_sub_prog');
    }
};
