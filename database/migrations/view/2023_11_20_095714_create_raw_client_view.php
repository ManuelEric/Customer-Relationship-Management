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
            rcp.uuid as parent_uuid,
            rcp.fullname as parent_name,
            rcp.mail as parent_mail,
            rcp.phone as parent_phone,
            rc.graduation_year,
            rc.role,
            rc.relation_key,
            rc.lead_id,
            (CASE 
                WHEN l.main_lead = "KOL" THEN CONCAT("KOL - ", l.sub_lead)
                ELSE l.main_lead
            END) AS lead_source,
            sch.sch_id,
            sch.sch_name AS school_name,
            rc.interest_countries,
            rc.created_at,
            rc.updated_at
            
        
        FROM tbl_raw_client rc
            LEFT JOIN tbl_raw_client rcp
                ON rcp.relation_key = rc.relation_key
                AND rcp.role = "parent"  
            LEFT JOIN tbl_lead l
                ON l.lead_id = rc.lead_id
            LEFT JOIN tbl_sch sch
                ON sch.sch_id = rc.sch_id
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
