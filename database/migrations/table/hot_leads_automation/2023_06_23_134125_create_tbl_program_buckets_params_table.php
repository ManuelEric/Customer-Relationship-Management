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
        Schema::create('tbl_program_buckets_params', function (Blueprint $table) {
            $table->id();
            $table->char('bucket_id', 8)->collation('utf8mb4_general_ci')->unique();
            $table->unsignedBigInteger('initialprogram_id');
            $table->unsignedBigInteger('param_id');
            $table->integer('weight');
            $table->enum('client', ['New', 'Existing Mentee', 'Existing NonMentee']);

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
        Schema::dropIfExists('tbl_program_buckets_params');
    }
};
