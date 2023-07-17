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
        Schema::create('tbl_client_lead_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('initialprogram_id');
            $table->foreign('initialprogram_id')->references('id')->on('tbl_initial_program_lead')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('type', ['Program', 'Lead']);
            $table->double('total_result');
            $table->tinyInteger('status')->default(0);

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
        Schema::dropIfExists('tbl_client_lead_tracking');
    }
};
