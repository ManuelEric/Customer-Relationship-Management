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
        Schema::create('tbl_partner_agreement', function (Blueprint $table) {
            $table->id();
            $table->char('corp_id', 9)->collation('utf8mb4_general_ci');
            $table->foreign('corp_id')->references('corp_id')->on('tbl_corp')->onUpdate('cascade')->onDelete('cascade');

            $table->string('agreement_name');
            $table->tinyInteger('agreement_type')->comment('0: Referral Mutual Agreement, 1: Partnership Agreement, 2: Speaker Agreement, 3: University Agent');
            $table->string('attachment');
            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedBigInteger('corp_pic');
            $table->foreign('corp_pic')->references('id')->on('tbl_corp_pic')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('empl_id');
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
        Schema::dropIfExists('tbl_partner_agreement');
    }
};
