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
        Schema::create('tbl_sch_prog', function (Blueprint $table) {
            $table->id();
            $table->char('sch_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('sch_id')->references('sch_id')->on('tbl_sch')->onUpdate('cascade')->onDelete('cascade');

            $table->char('prog_id', 11)->collation('utf8mb4_general_ci');
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->date('first_discuss')->nullable();
            $table->date('last_discuss')->nullable();
            $table->tinyInteger('status')->comment('0: Pending, 1: Success, 2: Denied');
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('empl_id')->comment('ALL-In PIC');
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
        Schema::dropIfExists('tbl_sch_prog');
    }
};
