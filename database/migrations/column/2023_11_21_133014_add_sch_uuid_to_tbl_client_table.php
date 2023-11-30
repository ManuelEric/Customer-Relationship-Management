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
        // Schema::table('tbl_client', function (Blueprint $table) {
        //     $table->string('sch_uuid', 39)->nullable()->after('sch_id')->collation('utf8mb4_general_ci');
        //     $table->foreign('sch_uuid')->references('uuid')->on('tbl_sch')->onUpdate('cascade')->onDelete('cascade');
        // });

        // DB::statement('
        //     UPDATE tbl_client c
        //     LEFT JOIN tbl_sch s ON s.sch_id = c.sch_id
        //     SET c.sch_uuid = s.uuid
        // ');

        // Schema::table('tbl_client', function (Blueprint $table) {
        //     $table->dropForeign('tbl_client_sch_id_foreign');
        //     $table->dropColumn('sch_id');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_client', function (Blueprint $table) {
            $table->char('sch_id', 8)->collation('utf8mb4_general_ci')->nullable()->after('sch_uuid');
        });
        
        DB::statement('
            UPDATE tbl_client c
            LEFT JOIN tbl_sch s ON s.uuid = c.sch_uuid
            SET c.sch_id = s.sch_id
        ');

        Schema::table('tbl_client', function (Blueprint $table) {
            $table->dropForeign('tbl_client_sch_uuid_foreign');
            $table->dropColumn('sch_uuid');
            $table->foreign('sch_id')->references('sch_id')->on('tbl_sch')->onUpdate('cascade')->onDelete('cascade');
        });

    }
};
