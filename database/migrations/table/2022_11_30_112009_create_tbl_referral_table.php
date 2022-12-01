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
        Schema::create('tbl_referral', function (Blueprint $table) {
            $table->id();
            $table->char('partner_id', 9)->collation('utf8mb4_general_ci');
            $table->foreign('partner_id')->references('corp_id')->on('tbl_corp')->onUpdate('cascade')->onDelete('cascade');

            $table->char('prog_id', 11)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('empl_id')->comment('Internal PIC');
            $table->foreign('empl_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            
            $table->enum('referral_type', ['In', 'Out']);
            $table->string('additional_prog_name')->nullable();
            $table->bigInteger('number_of_student')->default(0);
            $table->bigInteger('revenue')->default(0);
            $table->date('ref_date');
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
        Schema::dropIfExists('tbl_referral');
    }
};
