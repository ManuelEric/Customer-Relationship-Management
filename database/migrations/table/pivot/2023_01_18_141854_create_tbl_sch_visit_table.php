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
        Schema::create('tbl_sch_visit', function (Blueprint $table) {
            $table->id();
            $table->char('sch_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('sch_id')->references('sch_id')->on('tbl_sch')->onUpdate('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('internal_pic');
            $table->foreign('internal_pic')->references('id')->on('users')->onUpdate('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('school_pic');
            $table->foreign('school_pic')->references('schdetail_id')->on('tbl_schdetail')->onUpdate('cascade')->onUpdate('cascade');

            $table->date('visit_date');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('tbl_sch_visit');
    }
};
