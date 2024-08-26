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
        Schema::table('tbl_client_lead_tracking', function (Blueprint $table) {
            $table->unsignedBigInteger('reason_id')->nullable()->after('status');

            $table->foreign('reason_id')->references('reason_id')->on('tbl_reason')->onUpdate('cascade')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_lead_tracking', function (Blueprint $table) {
            //
        });
    }
};
