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
        Schema::create('tbl_events', function (Blueprint $table) {
            $table->char('event_id', 11)->collation('utf8mb4_general_ci')->primary();
            $table->string('event_title');
            $table->text('event_description')->nullable();
            $table->string('event_location')->nullable();
            $table->dateTime('event_startdate')->nullable();
            $table->dateTime('event_enddate')->nullable();
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
        Schema::dropIfExists('tbl_events');
    }
};
