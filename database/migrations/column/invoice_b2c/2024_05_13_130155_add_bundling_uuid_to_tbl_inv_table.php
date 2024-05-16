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
        Schema::table('tbl_inv', function (Blueprint $table) {
            $table->foreignUuid('bundling_id')->after('clientprog_id')->nullable()->references('uuid')->on('tbl_bundling')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_inv', function (Blueprint $table) {
            $table->dropForeign('tbl_inv_bundling_id_foreign');
            $table->dropColumn('bundling_id');
        });
    }
};
