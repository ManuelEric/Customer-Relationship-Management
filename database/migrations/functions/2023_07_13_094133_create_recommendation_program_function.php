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

        CREATE OR REPLACE FUNCTION GetScoreForAdmissionProgram ( 
            school_categorization INTEGER, 
            grade_categorization INTEGER,
            country_categorization INTEGER,
            roles VARCHAR(7),
            major_categorization INTEGER,
        )
        RETURNS DOUBLE(2,2)

        BEGIN
            DECLARE initial_program = 1;

            WHILE initial_program < 5 DO
                SELECT * FROM tbl_program_buckets_params 
                    WHERE initialprogram_id = initial_program
            END WHILE;
            
            


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
        Schema::dropIfExists('recommendation_program_function');
    }
};
