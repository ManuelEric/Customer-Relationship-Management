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
        Schema::table('tbl_receipt', function (Blueprint $table) {
            $table->char('inv_id', 50)->collation('utf8mb4_general_ci')->nullable()->change();
            $table->string('receipt_cheque', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_receipt', function (Blueprint $table) {
            //
        });
    }
};
