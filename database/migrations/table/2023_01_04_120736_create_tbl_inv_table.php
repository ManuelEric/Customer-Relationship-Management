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
        Schema::create('tbl_inv', function (Blueprint $table) {
            $table->id();
            $table->char('inv_id', 50)->collation('utf8mb4_general_ci')->unique();
            $table->unsignedBigInteger('clientprog_id');
            $table->foreign('clientprog_id')->references('clientprog_id')->on('tbl_client_prog')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->foreign('ref_id')->references('id')->on('tbl_referral')->onUpdate('cascade')->onDelete('cascade');
            $table->string('inv_category', 10); # will be filled with "usd" / "idr" / "session"
            $table->bigInteger('inv_price')->nullable();
            $table->bigInteger('inv_earlybird')->nullable();
            $table->bigInteger('inv_discount')->nullable();
            $table->bigInteger('inv_totalprice')->default(0);
            $table->text('inv_words')->nullable();
            $table->bigInteger('inv_price_idr')->nullable();
            $table->bigInteger('inv_earlybird_idr')->nullable();
            $table->bigInteger('inv_discount_idr')->nullable();
            $table->bigInteger('inv_totalprice_idr');
            $table->text('inv_words_idr')->nullable();
            $table->integer('session')->default(0);
            $table->integer('duration')->default(0);
            $table->enum('inv_paymentmethod', ['Full Payment', 'Installment']);
            $table->date('inv_duedate');
            $table->text('inv_notes')->nullable();
            $table->text('inv_tnc')->nullable();
            $table->boolean('inv_status')->default(false);
            $table->integer('curs_rate')->default(0);
            $table->enum('currency', ['gbp', 'usd', 'sgd', 'idr'])->default('idr');
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
        Schema::dropIfExists('tbl_inv');
    }
};
