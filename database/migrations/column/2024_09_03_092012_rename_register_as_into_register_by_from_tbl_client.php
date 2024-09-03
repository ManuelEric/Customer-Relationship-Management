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
        Schema::table('tbl_client', function (Blueprint $table) {
            DB::statement("ALTER TABLE `tbl_client` CHANGE `register_as` `register_by` ENUM('student', 'parent', 'teacher/counsellor') DEFAULT 'student'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client', function (Blueprint $table) {
            //
        });
    }
};
