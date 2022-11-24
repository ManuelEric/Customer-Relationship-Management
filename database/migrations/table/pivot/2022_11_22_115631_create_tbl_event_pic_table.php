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
        Schema::create('tbl_event_pic', function (Blueprint $table) {
            $table->id();
            $table->char('event_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('event_id')->references('event_id')->on('tbl_events')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('empl_id')->comment('internal pic');
            $table->foreign('empl_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tbl_event_pic');
    }
};
