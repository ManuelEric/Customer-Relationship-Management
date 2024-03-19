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
        Schema::create('tbl_followup_client', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp('followup_date');
            $table->text('notes')->nullable();
            $table->text('minutes_of_meeting')->nullable();
            $table->integer('status')->default(0)->comment('0: Not yet, 1: Done, 2: Pause, 3: Negotiation');
            $table->boolean('reminder_is_sent')->default(false);
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
        Schema::dropIfExists('tbl_followup_client');
    }
};
