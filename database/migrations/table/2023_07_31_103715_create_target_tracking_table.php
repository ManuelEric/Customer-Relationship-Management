<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('target_tracking', function (Blueprint $table) {
            $table->id();
            
            $table->string('divisi');
            $table->integer('target');
            $table->integer('achieved');
            $table->integer('added')->comment('the number of deviation from month before');
            $table->integer('month')->comment('month in number');
            $table->integer('year')->comment('year in number');
            $table->tinyInteger('status')->comment('0: incomplete, 1: complete')->default(0);

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
        Schema::dropIfExists('target_tracking');
    }
};
