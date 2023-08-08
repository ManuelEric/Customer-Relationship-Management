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
        Schema::table('tbl_seasonal_lead', function (Blueprint $table) {
            $table->dropForeign('tbl_seasonal_lead_initialprogram_id_foreign');
            $table->dropColumn('initialprogram_id');

            $table->char('prog_id', 11)->collation('utf8mb4_general_ci')->after('id');
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_seasonal_lead', function (Blueprint $table) {
            $table->char('prog_id', 11)->collation('utf8mb4_general_ci');
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('cascade');

            $table->dropColumn('prog_id');
        });
    }
};
