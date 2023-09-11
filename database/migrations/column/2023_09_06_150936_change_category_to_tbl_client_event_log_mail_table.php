<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            DB::statement("ALTER TABLE tbl_client_event_log_mail MODIFY COLUMN category ENUM('thanks-mail','qrcode-mail','qrcode-mail-referral','reminder-registration','reminder-referral','invitation-mail')");
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
            //
        });
    }
};
