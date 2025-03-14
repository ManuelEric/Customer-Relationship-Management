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
            $table->text('univ_requirement_link')->nullable()->after('univ_phone');
            $table->datetime('univ_application_deadline')->nullable()->after('univ_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_univ', function (Blueprint $table) {
            $table->dropColumn('univ_application_deadline');
        }); 
    }
};
