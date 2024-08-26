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
        Schema::create('tbl_partner_prog', function (Blueprint $table) {
            $table->id();
            $table->char('corp_id', 9)->collation('utf8mb4_general_ci');
            $table->foreign('corp_id')->references('corp_id')->on('tbl_corp')->onUpdate('cascade')->onDelete('cascade');

            $table->char('prog_id', 11)->collation('utf8mb4_general_ci');
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('type')->nullable();
            $table->date('first_discuss')->nullable();
            $table->date('last_discuss')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('status');
            
            $table->bigInteger('number_of_student')->default(0);
            $table->date('start_date');
            $table->date('end_date');

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
        Schema::dropIfExists('tbl_partner_prog');
    }
};
