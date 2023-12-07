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
        #
        Schema::create('tbl_raw_school', function (Blueprint $table) {
            $table->string('sch_id', 8)->unique();
            $table->string('uuid')->unique();
            $table->string('sch_name');

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
        Schema::dropIfExists('tbl_raw_school');
    }
};
