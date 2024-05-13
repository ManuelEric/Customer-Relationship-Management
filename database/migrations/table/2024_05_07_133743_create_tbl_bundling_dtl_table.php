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
        Schema::create('tbl_bundling_dtl', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('bundling_id')->references('uuid')->on('tbl_bundling')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('clientprog_id');
            $table->foreign('clientprog_id')->references('clientprog_id')->on('tbl_client_prog')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_bundling_dtl');
    }
};
