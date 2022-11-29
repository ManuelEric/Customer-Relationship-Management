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
        Schema::create('tbl_partner_prog_attachment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_prog_id');
            $table->foreign('partner_prog_id')->references('id')->on('tbl_partner_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->text('file_title');
            $table->text('attachment');
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
        Schema::dropIfExists('tbl_partner_prog_attachment');
    }
};
