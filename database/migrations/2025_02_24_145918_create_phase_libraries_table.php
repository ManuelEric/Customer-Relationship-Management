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
        Schema::create('phase_libraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_detail_id')->constrained(
                table: 'phase_details', indexName: 'phase_libraries_phase_detail_id'
            );
            $table->string('nation', 15);
            $table->integer('grade');
            $table->string('quota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_libraries');
    }
};
