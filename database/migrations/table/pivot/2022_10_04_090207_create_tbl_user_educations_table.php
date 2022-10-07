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
        Schema::create('tbl_user_educations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('univ_id');
            $table->foreign('univ_id')->references('id')->on('tbl_univ')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('major_id');
            $table->foreign('major_id')->references('id')->on('tbl_major')->onUpdate('cascade')->onDelete('cascade');
            
            $table->string('degree');
            $table->date('graduation_date');
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
        Schema::dropIfExists('tbl_user_educations');
    }
};
