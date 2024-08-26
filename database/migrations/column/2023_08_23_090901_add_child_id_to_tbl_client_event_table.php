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
        Schema::table('tbl_client_event', function (Blueprint $table) {
            $table->unsignedBigInteger('child_id')->nullable()->after('client_id');
            $table->foreign('child_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_event', function (Blueprint $table) {

        });
    }
};
