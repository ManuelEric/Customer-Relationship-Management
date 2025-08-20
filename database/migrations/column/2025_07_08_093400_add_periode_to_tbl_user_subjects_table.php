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
            $table->string('curriculum')->nullable()->after('subject_id');
            $table->date('end_date')->nullable()->comment('Active Agreement Periode')->after('subject_id');
            $table->date('start_date')->nullable()->comment('Active Agreement Periode')->after('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            $table->dropColumn('curriculum');
            $table->dropColumn('end_date');
            $table->dropColumn('start_date');
        });
    }
};
