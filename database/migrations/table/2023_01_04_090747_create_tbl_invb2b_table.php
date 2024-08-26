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
        Schema::create('tbl_invb2b', function (Blueprint $table) {
            $table->id('invb2b_num');
            $table->char('invb2b_id', 50)->collation('utf8mb4_general_ci')->unique();

            $table->unsignedBigInteger('schprog_id')->nullable();
            $table->foreign('schprog_id')->references('id')->on('tbl_sch_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('partnerprog_id')->nullable();
            $table->foreign('partnerprog_id')->references('id')->on('tbl_partner_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('invb2b_price')->nullable();
            $table->integer('invb2b_priceidr')->nullable();
            $table->integer('invb2b_participants');
            $table->integer('invb2b_disc')->nullable();
            $table->integer('invb2b_discidr')->nullable();
            $table->integer('invb2b_totprice')->nullable();
            $table->integer('invb2b_totpriceidr')->nullable();
            $table->text('invb2b_words')->nullable();
            $table->text('invb2b_wordsidr')->nullable();
            $table->date('invb2b_date');
            $table->date('invb2b_duedate');
            $table->string('invb2b_pm');
            $table->text('invb2b_notes')->nullable();
            $table->text('invb2b_tnc')->nullable();
            $table->tinyInteger('invb2b_status')->default('0');
            $table->bigInteger('curs_rate')->nullable();
            $table->enum('currency', ['gbp', 'usd', 'sgd'])->nullable();

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
        Schema::dropIfExists('tbl_invb2b');
    }
};
