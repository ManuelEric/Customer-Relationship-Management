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

        CREATE OR REPLACE FUNCTION UpdateGradeStudent ( ynow INTEGER, yinput INTEGER, mnow INTEGER, minput INTEGER, ginput INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE gradeNow INTEGER;

                IF (mnow >= 7 AND minput < 7) AND (ynow > yinput) THEN
                    SET gradeNow = (ynow - yinput) + (ginput + 1); 
                ELSEIF (mnow >= 7 AND minput < 7) AND (ynow = yinput) THEN
                    SET gradeNow = ginput + 1;  
                ELSEIF (mnow < 7 AND minput >= 7) AND (ynow = yinput) THEN
                    SET gradeNow = (ynow - yinput) + (ginput - 1);  
                ELSEIF ((mnow < 7 AND minput < 7) OR (mnow >= 7 AND minput >= 7)) AND (ynow = yinput) THEN
                    SET gradeNow = (ynow - yinput) + ginput;
                ELSE 
                    SET gradeNow = ginput;  
                END IF;

            RETURN gradeNow;
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
