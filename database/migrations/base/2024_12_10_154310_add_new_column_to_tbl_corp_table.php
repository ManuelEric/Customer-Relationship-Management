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
            $table->uuid('user_id')->nullable()->after('corp_name');
            $table->bigInteger('corp_subsector_id')->unsigned()->nullable()->after('corp_industry');
            $table->enum('corp_status', ['Contacted', 'Contracted', 'Engaged', 'Expired', 'Prospect'])->nullable()->after('partnership_type');
            $table->boolean('active_status')->default(1)->after('partnership_type');
            $table->string('corp_city')->after('corp_region')->nullable();
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
