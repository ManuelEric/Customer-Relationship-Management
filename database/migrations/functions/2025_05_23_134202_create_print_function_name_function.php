<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
        DELIMITER //

        CREATE FUNCTION StringProgramName ( clientprog_id INTEGER )
        RETURNS VARCHAR(255)
        
        BEGIN
        	DECLARE program_name VARCHAR(255) DEFAULT "";

            SELECT 
                (CASE
                    WHEN cp.package IS NOT NULL AND cp.curriculum IS NOT NULL THEN CONCAT(mp.prog_name, " : ", p.prog_program, " [", cp.package, "-", cp.curriculum, "]")
                    WHEN cp.package IS NOT NULL AND cp.curriculum IS NULL THEN CONCAT(mp.prog_name, " : ", p.prog_program, " [", cp.package, "]")
                    ELSE CONCAT(mp.prog_name, " : ", p.prog_program)
                END) INTO program_name
            FROM tbl_client_prog cp
                LEFT JOIN tbl_prog p ON p.prog_id = cp.prog_id
                LEFT JOIN tbl_main_prog mp ON mp.id = p.main_prog_id
                LEFT JOIN tbl_sub_prog sp ON sp.id = p.sub_prog_id
            WHERE cp.clientprog_id = clientprog_id;
            RETURN program_name;
        END; //
        DELIMITER ;
        ');
    }
};
