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
        Schema::table('tbl_invb2b', function (Blueprint $table) {

            // $table->enum('currency', ['gbp', 'usd', 'sgd', 'idr'])->nullable()->change();
            DB::statement("ALTER TABLE tbl_invb2b MODIFY COLUMN currency ENUM('gbp', 'usd', 'sgd', 'idr')");
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
            //
        });
    }
};
