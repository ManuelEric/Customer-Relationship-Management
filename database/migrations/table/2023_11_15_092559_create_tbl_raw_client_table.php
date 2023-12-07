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
        #
        Schema::create('tbl_raw_client', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('fullname');
            $table->string('mail')->nullable();
            $table->string('phone')->nullable();
            $table->enum('register_as', ['student', 'parent', 'teacher_counsellor'])->nullable();
            $table->enum('role', ['student', 'parent', 'teacher/counselor']);
            $table->string('relation_key', 5)->nullable();
            $table->string('school_uuid')->unique()->nullable();
            $table->text('interest_countries')->nullable();

            $table->string('lead_id', 5);
            $table->foreign('lead_id')->references('lead_id')->on('tbl_lead')->onUpdate('cascade')->onDelete('restrict');

            $table->char('graduation_year', 4)->nullable();
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
        Schema::dropIfExists('tbl_raw_client');
    }
};
