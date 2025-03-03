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
            $table->char('application_year', 4)->nullable()->after('grade_now');
            $table->enum('graduated_status', ['bachelors', 'masters'])->after('grade_now')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client', function (Blueprint $table) {
            $table->dropColumn('application_year');
            $table->dropColumn('graduated_status');
        });
    }
};
