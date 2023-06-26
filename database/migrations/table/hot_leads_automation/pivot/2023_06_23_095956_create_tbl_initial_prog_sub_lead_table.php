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
        Schema::create('tbl_initial_prog_sub_lead', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('initialprogram_id');
            $table->foreign('initialprogram_id')->references('id')->on('tbl_initial_program_lead')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('subprogram_id');
            $table->foreign('subprogram_id')->references('id')->on('tbl_sub_prog')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('tbl_initial_prog_sub_lead');
    }
};
