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
        Schema::table('tbl_program_lead_library', function (Blueprint $table) {
            $table->dropColumn('new_temp');
            $table->dropColumn('existing_mentee_temp');
            $table->dropColumn('existing_non_mentee_temp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_program_lead_library', function (Blueprint $table) {
            //
        });
    }
};
