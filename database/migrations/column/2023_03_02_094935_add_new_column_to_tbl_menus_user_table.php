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
        Schema::table('tbl_menus_user', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->boolean('export')->default(false)->after('user_id');
            $table->boolean('copy')->default(false)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_menus_user', function (Blueprint $table) {
            $table->dropColumn('export');
            $table->dropColumn('copy');
        });
    }
};
