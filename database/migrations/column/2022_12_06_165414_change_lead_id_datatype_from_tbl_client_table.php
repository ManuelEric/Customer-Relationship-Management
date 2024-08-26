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
        Schema::table('tbl_client', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
        });

        Schema::table('tbl_client', function (Blueprint $table) {

            $table->string('lead_id', 5)->change();
            $table->foreign('lead_id')->references('lead_id')->on('tbl_lead')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client', function (Blueprint $table) {
            //
        });
    }
};
