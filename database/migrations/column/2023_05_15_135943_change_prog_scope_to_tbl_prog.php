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
        Schema::table('tbl_prog', function (Blueprint $table) {
            DB::statement("ALTER TABLE tbl_prog MODIFY COLUMN prog_scope ENUM('public', 'mentee', 'school', 'partner')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_prog', function (Blueprint $table) {
            //
        });
    }
};
