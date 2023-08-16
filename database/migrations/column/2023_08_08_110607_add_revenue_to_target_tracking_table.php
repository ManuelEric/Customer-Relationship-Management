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
            $table->integer('revenue_target')->after('contribution_achieved');
            $table->integer('revenue_achieved')->after('contribution_achieved');
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
            //
        });
    }
};
