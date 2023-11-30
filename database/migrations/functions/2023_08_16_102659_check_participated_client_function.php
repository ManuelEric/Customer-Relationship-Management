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

        CREATE OR REPLACE FUNCTION checkParticipatedProgram ( requested_client_id INTEGER )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_program INTEGER DEFAULT 0;
            SELECT COUNT(*) INTO counted_program FROM tbl_client_prog cp
                    WHERE cp.client_id = requested_client_id;
                   RETURN counted_program;
        END; //
       DELIMITER ;
       ');

        DB::statement('
        DELIMITER //

        CREATE OR REPLACE FUNCTION checkParticipatedEvent ( requested_client_id INTEGER )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_event INTEGER DEFAULT 0;
            SELECT COUNT(*) INTO counted_event FROM tbl_client_event ce
                
                    WHERE ce.client_id = requested_client_id;
                   RETURN counted_event;
        END; //
       DELIMITER ;
       ');

        DB::statement('
        DELIMITER //

        CREATE OR REPLACE FUNCTION checkParticipated ( requested_client_id INTEGER )
        RETURNS VARCHAR(10)

            BEGIN
                DECLARE participated VARCHAR(20) COLLATE utf8mb4_general_ci;
                DECLARE join_program INTEGER DEFAULT 0; 
                DECLARE join_event INTEGER DEFAULT 0;

                SET join_program = checkParticipatedProgram(requested_client_id);
                SET join_event = checkParticipatedEvent(requested_client_id);

                IF join_program > 0 OR join_event > 1 THEN
                    SET participated = "Yes";
                ELSE
                    SET participated = "No";
                END IF;

                RETURN participated;
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
