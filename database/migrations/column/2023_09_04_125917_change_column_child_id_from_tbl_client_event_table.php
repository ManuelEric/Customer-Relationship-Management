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
            $table->unsignedBigInteger('child_id')->comment('is used when client_id is a parent / they registered as a parent')->change();

            $table->unsignedBigInteger('parent_id')->after('child_id')->nullable()->comment('is used when client_id is a student / they registered as a student');
            $table->foreign('parent_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropColumn('parent_id');
        });
    }
};
