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
        Schema::table('tbl_program_buckets_params', function (Blueprint $table) {
            $table->dropColumn('client');
            $table->dropColumn('weight');
            $table->integer('weight_new')->after('param_id')->nullable();
            $table->integer('weight_existing_mentee')->after('param_id')->nullable();
            $table->integer('weight_existing_non_mentee')->after('param_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_program_bucket_params', function (Blueprint $table) {
            //
        });
    }
};
