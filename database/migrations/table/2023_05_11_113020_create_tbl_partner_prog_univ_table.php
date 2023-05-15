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
        Schema::create('tbl_partner_prog_univ', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partnerprog_id');
            $table->foreign('partnerprog_id')->references('id')->on('tbl_partner_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->char('univ_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('univ_id')->references('univ_id')->on('tbl_univ')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_partner_prog_univ');
    }
};
