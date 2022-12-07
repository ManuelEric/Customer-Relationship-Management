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
            $table->char('event_id', 11)->collation('utf8mb4_general_ci')->after('eduf_id')->nullable();
            $table->foreign('event_id')->references('event_id')->on('tbl_events')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropColumn('event_id');
        });
    }
};
