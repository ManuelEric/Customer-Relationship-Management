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
        Schema::create('tbl_program_lead_library', function (Blueprint $table) {
            $table->id();
            $table->char('programbucket_id', 8)->collation('utf8mb4_general_ci');
            $table->integer('value_category');
            $table->boolean('new');
            $table->boolean('existing_mentee');
            $table->boolean('existing_non_mentee');
            $table->integer('new_temp')->default('0');
            $table->integer('existing_mentee_temp')->default('0');
            $table->integer('existing_non_mentee_temp')->default('0');

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
        Schema::dropIfExists('tbl_program_lead_library');
    }
};
