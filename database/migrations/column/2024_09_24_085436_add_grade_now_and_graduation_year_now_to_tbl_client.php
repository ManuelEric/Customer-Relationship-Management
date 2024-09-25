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
            $table->integer('grade_now')->nullable()->after('took_ia');
            $table->integer('graduation_year_now')->nullable()->after('took_ia');
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
