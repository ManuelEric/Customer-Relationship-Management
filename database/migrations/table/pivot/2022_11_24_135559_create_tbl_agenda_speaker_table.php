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
        Schema::create('tbl_agenda_speaker', function (Blueprint $table) {
            $table->id();

            $table->char('event_id', 11)->collation('utf8mb4_general_ci');
            $table->foreign('event_id')->references('event_id')->on('tbl_events')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('sch_prog_id')->nullable();
            $table->foreign('sch_prog_id')->references('id')->on('tbl_sch_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('partner_prog_id')->nullable();
            $table->foreign('partner_prog_id')->references('id')->on('tbl_partner_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('sch_pic_id')->nullable();
            $table->foreign('sch_pic_id')->references('schdetail_id')->on('tbl_schdetail')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('univ_pic_id')->nullable();
            $table->foreign('univ_pic_id')->references('id')->on('tbl_univ_pic')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('partner_pic_id')->nullable();
            $table->foreign('partner_pic_id')->references('id')->on('tbl_corp_pic')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('empl_id')->nullable()->comment('ALL-In PIC');
            $table->foreign('empl_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('priority');
            $table->boolean('status')->default(true);
            $table->enum('speaker_type', ['school', 'university', 'partner', 'internal']);
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
        Schema::dropIfExists('tbl_agenda_speaker');
    }
};
