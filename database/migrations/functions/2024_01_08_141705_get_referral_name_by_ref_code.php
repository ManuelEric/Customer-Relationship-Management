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

        CREATE OR REPLACE FUNCTION GetReferralNameByRefCode ( refCode VARCHAR(50) )
        RETURNS VARCHAR(255)
        
        BEGIN
        	DECLARE referral_name VARCHAR(255) DEFAULT NULL;

            SELECT full_name INTO referral_name FROM client_ref_code_view cref
                    WHERE cref.ref_code = refCode;
                   RETURN referral_name;
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
