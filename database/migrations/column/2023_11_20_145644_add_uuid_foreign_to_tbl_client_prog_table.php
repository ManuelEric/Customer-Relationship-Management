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
        # 1. Running this migration 
        # 2. Running client program view 
        # 3. Running function check participated 
        
        // Schema::table('tbl_client_prog', function (Blueprint $table) {
        //     $table->string('client_uuid', 36)->nullable()->after('client_id');
        //     $table->foreign('client_uuid')->references('uuid')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');
        // });

        // DB::statement('
        //     UPDATE tbl_client_prog cp 
        //     LEFT JOIN tbl_client c ON c.id = cp.client_id 
        //     SET cp.client_uuid = c.uuid
        // ');

        // Schema::table('tbl_client_prog', function (Blueprint $table) {
        //     $table->dropForeign('tbl_client_prog_client_id_foreign');
        //     $table->dropColumn('client_id'); 
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('clientprog_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');
        });

        DB::statement('
            UPDATE tbl_client_prog cp 
            LEFT JOIN tbl_client c ON c.uuid = cp.client_uuid 
            SET cp.client_id = c.id
        ');

        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->dropForeign('tbl_client_prog_client_uuid_foreign');
            $table->dropColumn('client_uuid');
        });
    }
};
