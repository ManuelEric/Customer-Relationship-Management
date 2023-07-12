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
        DB::statement('
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetClientType ( requested_client_id INTEGER )
        RETURNS VARCHAR(20)
        DETERMINISTIC

            BEGIN
                DECLARE client_type VARCHAR(20);
                DECLARE join_program_admission INTEGER; 
                DECLARE join_program_non_admission INTEGER;

                SELECT COUNT(*) INTO join_program_admission FROM tbl_client_prog cp
                    JOIN tbl_prog p ON p.prog_id = cp.prog_id
                        JOIN tbl_main_prog mp ON mp.id = p.main_prog_id
                        JOIN tbl_sub_prog sp ON sp.id = p.sub_prog_id
                    WHERE cp.client_id = requested_client_id
                        AND mp.prog_name = "Admissions Mentoring"

                SELECT COUNT(*) INTO join_program_non_admission FROM tbl_client_prog cp
                    JOIN tbl_prog p ON p.prog_id = cp.prog_id
                        JOIN tbl_main_prog mp ON mp.id = p.main_prog_id
                        JOIN tbl_sub_prog sp ON sp.id = p.sub_prog_id
                    WHERE cp.client_id = requested_client_id
                        AND mp.prog_name != "Admissions Mentoring"

                IF join_program_admission > 0 THEN
                    SET client_type = "existing-mentee"
                ELSE IF join_program_non_admission > 0 THEN
                    SET client_type = "existing-non-mentee"
                ELSE IF join_program_admission = 0 AND join_program_non_admission = 0
                    SET client_type = "new";
                END IF;

            RETURN client_type;
        END; //

        DELIMITER ;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('get_client_type_function');
    }
};
