<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_program_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clientprog_id');
            $table->foreign('clientprog_id')->references('clientprog_id')->on('tbl_client_prog')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('phase_lib_id')->constrained(
                table: 'phase_libraries', indexName: 'client_program_details_phase_lib_id',
            )->onUpdate('cascade')->onDelete('cascade');
            $table->string('quota');
            $table->integer('grade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_program_details');
    }
};
