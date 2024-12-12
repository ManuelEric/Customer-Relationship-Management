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
        Schema::table('tbl_corp', function (Blueprint $table) {
            $table->bigInteger('corp_industry')->unsigned()->nullable()->change();
            $table->foreign('corp_industry')->references('id')->on('tbl_industry');
            $table->foreign('corp_subsector_id')->references('id')->on('tbl_industry_subsector');
            $table->foreign('user_id')->references('id')->on('users');
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
