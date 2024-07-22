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
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            DB::statement("ALTER TABLE tbl_user_subjects MODIFY COLUMN year year(4) NULL");
            $table->text('agreement')->nullable()->change();
            $table->string('grade')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            //
        });
    }
};
