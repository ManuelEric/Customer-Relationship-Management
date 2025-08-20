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
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->string('curriculum')->comment('Tutoring curriculum')->nullable()->after('empl_id');
            $table->string('package')->comment('Tutoring package')->nullable()->after('empl_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->dropColumn('curriculum');
            $table->dropColumn('package');
        });
    }
};
