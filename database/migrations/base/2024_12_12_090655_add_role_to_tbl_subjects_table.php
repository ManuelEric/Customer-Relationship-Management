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
        Schema::table('tbl_subjects', function (Blueprint $table) {
            $table->enum('role', ['Tutor', 'External Mentor', 'Editor', 'Individual Professional'])->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_subjects', function (Blueprint $table) {
            //
        });
    }
};
