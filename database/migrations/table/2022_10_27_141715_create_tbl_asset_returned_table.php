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
        Schema::create('tbl_asset_returned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_used_id');
            $table->foreign('asset_used_id')->references('id')->on('tbl_asset_used')->onUpdate('cascade')->onDelete('cascade');

            $table->date('returned_date');
            $table->integer('amount_returned')->default(1);
            $table->enum('condition', ['Good', 'Not Good'])->default('Good');
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
        Schema::dropIfExists('tbl_asset_returned');
    }
};
