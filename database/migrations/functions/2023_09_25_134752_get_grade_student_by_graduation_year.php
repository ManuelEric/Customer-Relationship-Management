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

        CREATE OR REPLACE FUNCTION getGradeStudentByGraduationYear ( graduation_year INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE grade INTEGER;
                DECLARE month_now INTEGER;
                DECLARE diff_year INTEGER;
                
                SET diff_year = graduation_year - YEAR(NOW());
                SET grade = 12 - diff_year;
                SET month_now = MONTH(now());

                IF (month_now < 7) THEN 
                    SET grade = grade + 1;
                END IF;

            RETURN grade;
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
        //
    }
};
