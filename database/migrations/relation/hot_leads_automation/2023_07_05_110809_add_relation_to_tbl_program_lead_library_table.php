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
            $table->foreign('leadbucket_id')->references('bucket_id')->on('tbl_lead_bucket_params')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_program_lead_library', function (Blueprint $table) {
            //
        });
    }
};
