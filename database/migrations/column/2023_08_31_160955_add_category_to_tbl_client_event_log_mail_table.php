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
        Schema::table('tbl_client_event_log_mail', function (Blueprint $table) {
            $table->enum('category', ['thanks-mail', 'qrcode-mail', 'reminder-mail'])->nullable()->after('sent_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_event_log_mail', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
