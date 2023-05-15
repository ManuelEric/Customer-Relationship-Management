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
        Schema::table('tbl_corp_pic', function (Blueprint $table) {
            $table->boolean('is_pic')->default(false)->after('pic_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_corp_pic', function (Blueprint $table) {
            $table->dropColumn('is_pic');
        });
    }
};
