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
        Schema::table('tbl_invdtl', function (Blueprint $table) {
            $table->integer('reminded')->default(0)->after('invdtl_currency')->comment('has been reminded = 1 else 0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_invdtl', function (Blueprint $table) {
            $table->dropColumn('reminded');
        });
    }
};
