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
        Schema::table('tbl_client', function (Blueprint $table) {
            $table->text('mentoring_google_drive_link')->nullable()->after('blacklist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client', function (Blueprint $table) {
            $table->dropColumn('mentoring_google_drive_link');
        });
    }
};
