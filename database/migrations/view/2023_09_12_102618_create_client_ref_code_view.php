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

        CREATE OR REPLACE FUNCTION CreateRefCode ( identifier INTEGER )
        RETURNS VARCHAR(10)
        DETERMINISTIC

            BEGIN
                DECLARE ref_code VARCHAR(10);

                SELECT CONCAT(UPPER(SUBSTR(first_name, 1, 3)), id) INTO ref_code 
                    FROM tbl_client
                    WHERE id = identifier;
            RETURN ref_code;
        END; //

        DELIMITER ;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW client_ref_code_view AS
            SELECT id, first_name, last_name, CreateRefCode(id) as ref_code
            FROM tbl_client;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
};
