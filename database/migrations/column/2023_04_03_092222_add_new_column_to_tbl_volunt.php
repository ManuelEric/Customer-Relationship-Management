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
        Schema::table('tbl_volunt', function (Blueprint $table) {
            $table->String('volunt_cv')->after('volunt_npwp')->default(null);
            $table->string('volunt_bank_accname')->after('volunt_npwp');
            $table->bigInteger('volunt_bank_accnumber')->after('volunt_npwp');
            $table->bigInteger('volunt_nik')->after('volunt_npwp');
            $table->bigInteger('volunt_npwp_number')->nullable()->after('volunt_npwp');
            $table->string('health_insurance')->nullable()->after('volunt_npwp');
            $table->string('empl_insurance')->nullable()->after('volunt_npwp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_volunt', function (Blueprint $table) {
        });
    }
};
