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
            $table->string('ticket_id', 10)->comment('can be used as identifier')->after('clientevent_id')->nullable();
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
            $table->dropColumn('ticket_id');
        });
    }
};
