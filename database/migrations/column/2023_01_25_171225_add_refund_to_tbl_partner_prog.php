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
        Schema::table('tbl_partner_prog', function (Blueprint $table) {
            $table->date('refund_date')->nullable()->after('notes');
            $table->text('refund_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_partner_prog', function (Blueprint $table) {
            //
        });
    }
};
