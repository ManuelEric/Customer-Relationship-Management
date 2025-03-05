<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_client_event_log_mail', function (Blueprint $table) {
            DB::statement("ALTER TABLE tbl_client_event_log_mail CHANGE COLUMN category category ENUM('thanks-mail','qrcode-mail','qrcode-mail-referral','reminder-registration','reminder-referral','reminder-attend','invitation-mail','thanks-mail-after','feedback-mail','invitation-info','reminder-mail','registration-event-mail','verification-registration-event-mail', 'email-confirmation-event') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client_event_log_mail', function (Blueprint $table) {
            //
        });
    }
};
