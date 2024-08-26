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
        Schema::table('tbl_purchase_request', function (Blueprint $table) {
            $table->date('purchase_requestdate')->after('purchase_statusrequest');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_purchase_request', function (Blueprint $table) {
            $table->dropColumn('purchase_requestdate');
        });
    }
};
