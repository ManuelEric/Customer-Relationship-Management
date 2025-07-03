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
            $table->integer('month_end')->comment('month to end')->nullable()->after('subject_id');
            $table->integer('month_start')->comment('month to start')->nullable()->after('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            $table->dropColumn('month_end');
            $table->dropColumn('month_start');
        });
    }
};
