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
        Schema::create('tbl_eduf_review', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eduf_id');
            $table->foreign('eduf_id')->references('id')->on('tbl_eduf_lead')->onUpdate('cascade')->onDelete('cascade');

            $table->string('reviewer_name');
            $table->string('score', 50);
            $table->text('review');

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
        Schema::dropIfExists('tbl_eduf_review');
    }
};
