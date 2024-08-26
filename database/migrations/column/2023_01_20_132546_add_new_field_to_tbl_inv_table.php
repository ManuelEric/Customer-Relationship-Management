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
        Schema::table('tbl_inv', function (Blueprint $table) {
            $table->enum('send_to_client', ['sent', 'not sent'])->default('not sent')->after('currency');
            $table->text('attachment')->after('currency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_inv', function (Blueprint $table) {
            $table->dropColumn('attachment');
            $table->dropColumn('send_to_client');
        });
    }
};
