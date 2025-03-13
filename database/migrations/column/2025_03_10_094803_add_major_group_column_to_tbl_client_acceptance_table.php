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
            $table->string('major_name')->nullable()->after('univ_id');
            $table->foreignId('major_group_id')->nullable()->after('univ_id')->constrained(
                table: 'major_groups', indexName: 'tbl_client_acceptance_major_group_id'
            )->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('major_id')->nullable()->constrained(
                table: 'tbl_major', indexName: 'tbl_client_acceptance_major_id'
            )->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client_acceptance', function (Blueprint $table) {
            $table->dropColumn('major_group');
        });
    }
};
