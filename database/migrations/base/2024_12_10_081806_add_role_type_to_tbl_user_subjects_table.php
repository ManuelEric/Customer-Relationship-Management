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
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            $table->enum('role_type', 
                [
                    'Competition Project Mentorship', 
                    'Research Project Mentorship',
                    'Passion Project Mentorship',
                    'Professional Sharing Session Speaker',
                    'Part Time Subject Mentor',
                    'Essay Mentor',
                    'Essay Editor'
                ])->nullable()->after('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            //
        });
    }
};
