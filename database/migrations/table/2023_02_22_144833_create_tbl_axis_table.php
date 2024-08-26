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
        Schema::create('tbl_axis', function (Blueprint $table) {
            $table->id();
            $table->float('top');
            $table->float('left');
            $table->float('scaleX');
            $table->float('scaleY');
            $table->float('angle');
            $table->tinyInteger('flipX')->comment('0: False, 1: True');
            $table->tinyInteger('flipY')->comment('0: False, 1: True');
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
        Schema::dropIfExists('tbl_axis');
    }
};
