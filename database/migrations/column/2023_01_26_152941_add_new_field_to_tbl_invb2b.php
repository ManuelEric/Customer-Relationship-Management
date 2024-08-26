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
        Schema::table('tbl_invb2b', function (Blueprint $table) {
            $table->enum('sign_status', ['not yet', 'signed'])->default('not yet')->after('currency');
            $table->date('approve_date')->after('currency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_invb2b', function (Blueprint $table) {
            $table->dropColumn('sign_status');
            $table->dropColumn('approve_date');
        });
    }
};
