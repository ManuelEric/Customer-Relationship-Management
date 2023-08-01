<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target_tracking', function (Blueprint $table) {
            $table->dropColumn('target');
            $table->dropColumn('achieved');
            $table->dropColumn('month');
            $table->dropColumn('year');

            $table->date('month_year')->after('divisi');

            $table->integer('contribution_achieved')->after('divisi');
            $table->integer('contribution_target')->after('divisi');

            $table->integer('achieved_initconsult')->after('divisi');
            $table->integer('target_initconsult')->after('divisi');
            
            $table->integer('achieved_hotleads')->after('divisi');
            $table->integer('target_hotleads')->after('divisi');
            
            $table->integer('achieved_lead')->after('divisi');
            $table->integer('target_lead')->after('divisi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('target_tracking', function (Blueprint $table) {
            
        });
    }
};
