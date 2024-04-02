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
        Schema::table('tbl_client_mentor', function (Blueprint $table) {
            $table->tinyInteger('type')->after('timesheet_link')->comment('1: Supervising Mentor, 2: Profile Building & Exploration Mentor, 3: Aplication Strategy Mentor, 4: Writing Mentor, 5: Tutor')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_mentor', function (Blueprint $table) {
            //
        });
    }
};
