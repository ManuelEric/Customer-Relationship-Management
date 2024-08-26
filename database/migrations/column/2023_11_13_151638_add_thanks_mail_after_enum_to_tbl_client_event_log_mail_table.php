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
        DB::statement("ALTER TABLE tbl_client_event_log_mail MODIFY `category` ENUM ('thanks-mail', 'qrcode-mail', 'qrcode-mail-referral', 'reminder-registration', 'reminder-referral', 'reminder-attend', 'invitation-mail', 'thanks-mail-after')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE tbl_client_event_log_mail MODIFY `category` ENUM ('thanks-mail', 'qrcode-mail', 'qrcode-mail-referral', 'reminder-registration', 'reminder-referral', 'reminder-attend', 'invitation-mail')");
    }
};
