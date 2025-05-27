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
        Schema::table('tbl_univ', function (Blueprint $table) {
            $table->dropColumn('univ_application_deadline');
            $table->dateTime('regular_deadline')->nullable()->after('univ_phone')->comment('Regular Deadline');
            $table->dateTime('early_decision')->nullable()->after('univ_phone')->comment('Early application (early decision)');
            $table->dateTime('early_action')->nullable()->after('univ_phone')->comment('Early application (early action)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_univ', function (Blueprint $table) {
            $table->dropColumn('regular_deadline');
            $table->dropColumn('early_decision');
            $table->dropColumn('early_action');
            $table->dateTime('univ_application_deadline')->nullable()->after('univ_phone')->comment('Application Deadline');
        });
    }
};
