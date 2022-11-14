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
        Schema::create('tbl_asset_used', function (Blueprint $table) {
            $table->id();

            $table->char('asset_id', 7)->collation('utf8mb4_general_ci');
            $table->foreign('asset_id')->references('asset_id')->on('tbl_asset')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->date('used_date');
            $table->integer('amount_used')->default(1);
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
        Schema::dropIfExists('tbl_asset_used');
    }
};
