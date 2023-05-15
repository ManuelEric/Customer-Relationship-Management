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
        Schema::create('tbl_sch_prog_partner', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('schprog_id');
            $table->foreign('schprog_id')->references('id')->on('tbl_sch_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->char('corp_id', 9)->collation('utf8mb4_general_ci');
            $table->foreign('corp_id')->references('corp_id')->on('tbl_corp')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('tbl_sch_prog_partner');
    }
};
