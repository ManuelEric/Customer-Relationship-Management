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
        CREATE OR REPLACE VIEW raw_client AS
        SELECT 
            rc.id,
            rc.fullname,
            rc.mail,
            rc.phone,
            rcp.fullname as parent_name,
            rcp.mail as parent_mail,
            rcp.phone as parent_phone,
            rc.graduation_year,
            (CASE 
                WHEN l.main_lead = "KOL" THEN CONCAT("KOL - ", l.sub_lead)
                ELSE l.main_lead
            END) AS lead_source,
            (CASE 
                WHEN SUBSTR(rc.school_uuid, 1, 2) = "rs" THEN rs.sch_name
                ELSE null
            END) AS school,
            rc.interest_countries,
            rc.created_at,
            rc.updated_at
            
        
        FROM tbl_raw_client rc
            LEFT JOIN tbl_raw_client rcp
                ON rcp.relation_key = rc.relation_key
                AND rcp.role = "parent"  
            LEFT JOIN tbl_lead l
                ON l.lead_id = rc.lead_id
            LEFT JOIN tbl_raw_school rs
                ON rs.uuid = rc.school_uuid

        where rc.role = "student"
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_student_view');
    }
};
