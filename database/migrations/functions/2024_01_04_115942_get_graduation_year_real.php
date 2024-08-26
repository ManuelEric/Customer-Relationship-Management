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

        CREATE OR REPLACE FUNCTION getGraduationYearReal ( grade_now INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE graduation_year_real INTEGER;
                DECLARE year_now INTEGER;
                DECLARE month_now INTEGER;
                
                SET year_now = YEAR(now());
                SET month_now = MONTH(now());

                IF (month_now >= 7) THEN 
                    SET graduation_year_real = (12 - grade_now) + year_now + 1;
                ELSE
                    SET graduation_year_real = (12 - grade_now) + year_now;
                END IF;

            RETURN graduation_year_real;
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
