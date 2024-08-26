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
        Schema::create('tbl_eduf_lead', function (Blueprint $table) {
            $table->id();

            // $table->string('organizer_name');
            $table->char('sch_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('sch_id')->references('sch_id')->on('tbl_sch')->onUpdate('cascade')->onDelete('restrict');

            $table->char('corp_id', 9)->collation('utf8mb4_general_ci');
            $table->foreign('corp_id')->references('corp_id')->on('tbl_corp')->onUpdate('cascade')->onDelete('restrict');

            $table->text('location');
            $table->string('intr_pic');
            $table->string('ext_pic_name');
            $table->string('ext_pic_mail');
            $table->string('ext_pic_phone');
            $table->date('first_discussion_date')->nullable();
            $table->date('last_discussion_date')->nullable();
            $table->date('event_start');
            $table->date('event_end');
            $table->boolean('status')->default(1);
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('tbl_eduf_lead');
    }
};
