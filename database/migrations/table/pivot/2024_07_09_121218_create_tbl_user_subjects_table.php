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
        Schema::create('tbl_user_subjects', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_role_id');
            $table->foreign('user_role_id')->references('id')->on('tbl_user_roles')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('tbl_subjects')->onUpdate('cascade')->onDelete('cascade');

            $table->bigInteger('fee_hours')->nullable();
            $table->bigInteger('fee_session')->nullable();

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
        Schema::dropIfExists('tbl_user_subjects');
    }
};
