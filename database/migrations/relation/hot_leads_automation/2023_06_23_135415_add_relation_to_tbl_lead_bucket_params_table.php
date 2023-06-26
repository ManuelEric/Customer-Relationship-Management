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
        Schema::table('tbl_lead_bucket_params', function (Blueprint $table) {
            $table->foreign('initialprogram_id')->references('id')->on('tbl_initial_program_lead')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('param_id')->references('id')->on('tbl_param_lead')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_lead_bucket_params', function (Blueprint $table) {
            //
        });
    }
};
