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
            $table->boolean('is_active')->default(true)->after('capacity');
            $table->timestamp('deactivated_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user_roles', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('deactivated_at');
        });
    }
};
