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
        Schema::table('tbl_program_lead_library', function (Blueprint $table) {
            $table->foreign('programbucket_id')->references('bucket_id')->on('tbl_program_buckets_params')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_lead_library', function (Blueprint $table) {
            //
        });
    }
};
