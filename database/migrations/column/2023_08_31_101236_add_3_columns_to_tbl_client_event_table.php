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
            $table->string('referral_code')->after('partner_id')->nullable()->comment('Referral code is a unique code from client data');
            $table->text('notes')->nullable()->after('partner_id');
            $table->integer('number_of_attend')->after('partner_id')->default(1)->comment('How many people are joined the event');
            $table->enum('registration_type', ['OTS', 'PR'])->after('partner_id')->default('PR')->comment('PR : Pra Registration, OTS : On The Spot');
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
            $table->dropColumn('referral_code');
            $table->dropColumn('notes');
            $table->dropColumn('number_of_attend');
            $table->dropColumn('registration_type');
        });
    }
};
