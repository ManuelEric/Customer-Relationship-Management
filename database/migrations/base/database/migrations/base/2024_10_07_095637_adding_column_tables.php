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
            $table->integer('status')->comment('0: pending, 1: success, 2: failed, 3: refund, 4: hold')->change();
            $table->datetime('hold_date')->comment('a date that created to inform when holding process started')->after('empl_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->dropColumn('hold_date');
        });
    }
};