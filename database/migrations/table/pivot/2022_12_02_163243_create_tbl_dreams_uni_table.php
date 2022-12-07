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
        Schema::create('tbl_dreams_uni', function (Blueprint $table) {
            $table->id();
            $table->char('univ_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('univ_id')->references('univ_id')->on('tbl_univ')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_dreams_uni');
    }
};
