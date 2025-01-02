<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_corp', function (Blueprint $table) {
            DB::statement("ALTER TABLE tbl_corp CHANGE COLUMN partnership_type partnership_type ENUM('Market Sharing/Referral Collaboration', 'Program Collaboration', 'Program Contributor', 'Speaker', 'Volunteer', 'Internship', 'Company Visit') NULL ");
            DB::statement("ALTER TABLE tbl_corp CHANGE COLUMN type type ENUM('Corporate','Individual/Professionals','Course Center','Agent','Community/NGO','University') NULL ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_corp', function (Blueprint $table) {
            //
        });
    }
};
