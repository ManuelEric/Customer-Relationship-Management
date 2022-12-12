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
        Schema::table('tbl_partner_prog_attachment', function (Blueprint $table) {
            $table->renameColumn('file_title', 'corprog_file');
            $table->renameColumn('attachment', 'corprog_attach');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_partner_prog_attachment', function (Blueprint $table) {
            //
        });
    }
};
