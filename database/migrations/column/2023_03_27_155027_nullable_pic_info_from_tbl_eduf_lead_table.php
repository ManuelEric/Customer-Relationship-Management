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
        Schema::table('tbl_eduf_lead', function (Blueprint $table) {
            $table->string('ext_pic_name')->nullable()->change();
            $table->string('ext_pic_mail')->nullable()->change();
            $table->string('ext_pic_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_eduf_lead', function (Blueprint $table) {
            //
        });
    }
};
