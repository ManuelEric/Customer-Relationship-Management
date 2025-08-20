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
        Schema::table('tbl_client_acceptance', function (Blueprint $table) {
            $table->datetime('regular_deadline')->nullable()->after('requirement_link');
            $table->datetime('early_decision')->nullable()->after('requirement_link');
            $table->datetime('early_action')->nullable()->after('requirement_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client_acceptance', function (Blueprint $table) {
            $table->dropColumn('early_action');
            $table->dropColumn('early_decision');
            $table->dropColumn('regular_deadline');
        });
    }
};
