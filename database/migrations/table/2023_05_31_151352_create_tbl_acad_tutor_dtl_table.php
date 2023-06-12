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
        Schema::create('tbl_acad_tutor_dtl', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('clientprog_id');
            $table->foreign('clientprog_id')->references('clientprog_id')->on('tbl_client_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->date('date');
            $table->time('time');
            $table->text('link')->comment('online meeting room');

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
        Schema::dropIfExists('tbl_acad_tutor_dtl');
    }
};
