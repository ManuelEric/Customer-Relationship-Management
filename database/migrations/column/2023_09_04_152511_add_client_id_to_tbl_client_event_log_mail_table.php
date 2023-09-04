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
            $table->unsignedBigInteger('client_id')->nullable()->after('clientevent_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE tbl_client_event_log_mail MODIFY COLUMN category ENUM("thanks-mail", "qrcode-mail", "reminder-mail", "invitation-mail")');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_event_log_mail', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');

            DB::statement('ALTER TABLE tbl_client_event_log_mail MODIFY COLUMN category ENUM("thanks-mail", "qrcode-mail", "reminder-mail")');
        });
    }
};
