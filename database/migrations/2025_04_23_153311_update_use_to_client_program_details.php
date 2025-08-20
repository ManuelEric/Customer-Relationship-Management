<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_program_details', function (Blueprint $table) {
            DB::statement('ALTER TABLE client_program_details MODIFY COLUMN `use` double(8,1) NULL');
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
