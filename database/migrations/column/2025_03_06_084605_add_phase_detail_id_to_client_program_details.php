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
        Schema::table('client_program_details', function (Blueprint $table) {
            $table->unsignedBigInteger('phase_lib_id')->nullable()->change();
            $table->unsignedBigInteger('phase_detail_id')->nullable()->after('clientprog_id');

            $table->foreign('phase_detail_id')->references('id')->on('phase_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_program_details', function (Blueprint $table) {
            //
        });
    }
};
