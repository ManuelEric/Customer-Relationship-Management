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
        Schema::create('tbl_event_speaker', function (Blueprint $table) {
            $table->id();

            $table->char('event_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('event_id')->references('event_id')->on('tbl_events')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('sch_pic_id')->nullable();
            $table->foreign('sch_pic_id')->references('schdetail_id')->on('tbl_schdetail')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('univ_pic_id')->nullable();
            $table->foreign('univ_pic_id')->references('id')->on('tbl_univ_pic')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('corp_pic_id')->nullable();
            $table->foreign('corp_pic_id')->references('id')->on('tbl_corp_pic')->onUpdate('cascade')->onDelete('cascade');

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('priority');
            $table->boolean('status')->default(1);

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
        Schema::dropIfExists('tbl_event_speaker');
    }
};
