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
        Schema::create('tbl_client_event', function (Blueprint $table) {
            $table->unsignedBigInteger('clientevent_id')->autoIncrement();
            
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('restrict');

            $table->char('event_id', 11)->collation('utf8mb4_general_ci');
            $table->foreign('event_id')->references('event_id')->on('tbl_events')->onUpdate('cascade')->onDelete('restrict');

            $table->string('lead_id', 5);
            $table->foreign('lead_id')->references('lead_id')->on('tbl_lead')->onUpdate('cascade')->onDelete('restrict');

            $table->integer('status');
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
        Schema::dropIfExists('tbl_client_event');
    }
};
