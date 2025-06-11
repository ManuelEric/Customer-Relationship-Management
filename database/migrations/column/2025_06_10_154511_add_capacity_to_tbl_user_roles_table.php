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
        Schema::table('tbl_user_roles', function (Blueprint $table) {
            $table->integer('capacity')->comment('used for mentors to determine how many mentee\'s he/she can handle')->nullable()->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user_roles', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
};
