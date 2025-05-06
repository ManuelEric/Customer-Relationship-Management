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

        DROP PROCEDURE IF EXISTS ViewStudentFullInformation //

        CREATE PROCEDURE 
            ViewStudentFullInformation ( student_uuid CHAR(36) )
        BEGIN
            SELECT 
                c.id,
                CONCAT(c.first_name, " ", c.last_name) AS full_name,
                c.mail,
                c.phone,
                c.grade_now,
                s.sch_name
            FROM
                tbl_client c
            LEFT JOIN
                tbl_sch s ON c.sch_id = s.sch_id
            WHERE
                c.id = student_uuid
            ;
        END //
        DELIMITER ;
        ');
    }
};
