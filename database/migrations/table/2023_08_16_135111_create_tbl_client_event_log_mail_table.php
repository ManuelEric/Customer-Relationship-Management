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
        Schema::create('tbl_client_event_log_mail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientevent_id');
            $table->foreign('clientevent_id')->references('id')->on('tbl_client_event')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('sent_status')->default(false);
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
        Schema::dropIfExists('tbl_client_event_log_mail');
    }
};
