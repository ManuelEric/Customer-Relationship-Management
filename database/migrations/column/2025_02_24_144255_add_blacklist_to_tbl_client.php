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
            $table->boolean('blacklist')->default(false)->comment('0: No, 1: Yes')->after('grade_now');
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
