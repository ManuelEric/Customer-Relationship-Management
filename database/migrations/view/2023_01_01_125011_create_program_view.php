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
        DB::statement("
        CREATE OR REPLACE VIEW program AS
        SELECT 
            pr.prog_id as prog_id,
            pr.main_prog_id as main_prog_id,
            pr.sub_prog_id as sub_prog_id,
            pr.prog_type,
            pr.prog_mentor,
            pr.prog_payment,
            pr.prog_scope,
            pr.prog_program as prog_program,
            mp.prog_name as main_prog_name,
            sp.sub_prog_name as sub_prog_name,
            pr.active,
            pr.created_at,
            (CASE WHEN pr.sub_prog_id > 0 THEN
                (CASE WHEN mp.prog_name = sp.sub_prog_name THEN
                    CONCAT(mp.prog_name COLLATE utf8mb4_unicode_ci, ' : ', pr.prog_program COLLATE utf8mb4_unicode_ci)
                ELSE 
                    CONCAT(mp.prog_name COLLATE utf8mb4_unicode_ci, ' / ', sp.sub_prog_name COLLATE utf8mb4_unicode_ci, ' : ', pr.prog_program COLLATE utf8mb4_unicode_ci) 
                END)
            ELSE
                CONCAT(mp.prog_name COLLATE utf8mb4_unicode_ci, ' : ', pr.prog_program COLLATE utf8mb4_unicode_ci)
            END) as program_name

        FROM tbl_prog pr
        LEFT JOIN tbl_main_prog mp 
            ON mp.id = pr.main_prog_id
            LEFT JOIN tbl_sub_prog sp 
                ON sp.id = pr.sub_prog_id 
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('program_view');
    }
};
