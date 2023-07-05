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
        Schema::table('tbl_lead_bucket_params', function (Blueprint $table) {
            $table->char('bucket_id', 8)->collation('utf8mb4_general_ci')->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_lead_bucket_params', function (Blueprint $table) {
            //
        });
    }
};
