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
        CREATE OR REPLACE VIEW clientprogram AS
        SELECT cp.*, 
            c.st_grade,
            r.reason_name as reason,
            CONCAT(c.first_name, " ", COALESCE(c.last_name, "")) as fullname,
            sch.sch_name as school_name,
            sch.sch_id,
            CONCAT(parent.first_name, " ", COALESCE(parent.last_name, "")) as parent_fullname,
            parent.phone as parent_phone,
            parent.mail as parent_mail,
            p.program_name,
            (CASE WHEN cp.status = 0 THEN "Pending"
                WHEN cp.status = 1 THEN "Success"
                WHEN cp.status = 2 THEN "Failed"
                WHEN cp.status = 3 THEN "Refund"
            END) AS program_status,
            CONCAT(u.first_name, " ", u.last_name) AS pic_name,
            u.email as pic_mail,
            (CASE 
                WHEN cl.department_id = 1 THEN "Sales"
                WHEN cl.department_id = 2 THEN "Partnership"
                WHEN cl.department_id = 7 THEN "Digital"
            END) AS lead_from,            
            cl.lead_id as lead_source_id,
            (CASE 
                WHEN cl.main_lead = "KOL" THEN CONCAT("KOL - ", cl.sub_lead)
                WHEN cl.main_lead = "External Edufair" THEN CONCAT("External Edufair - ", cedl.title)
                WHEN cl.main_lead = "All-In Event" THEN CONCAT("All-In Event - ", cec.event_title)
                ELSE cl.main_lead
            END) AS lead_source,
            (CASE 
                WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = "KOL" THEN CONCAT("KOL - ", cpl.sub_lead COLLATE utf8mb4_unicode_ci)
                WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = "External Edufair" THEN CONCAT("External Edufair - ", edl.title COLLATE utf8mb4_unicode_ci)
                WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = "All-In Event" THEN CONCAT("All-In Event - ", e.event_title COLLATE utf8mb4_unicode_ci)
                WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = "All-In Partners" THEN CONCAT("All-In Partner - ", corp.corp_name COLLATE utf8mb4_unicode_ci)
                ELSE cpl.main_lead COLLATE utf8mb4_unicode_ci
            END) AS conversion_lead,
            DATEDIFF(cp.first_discuss_date, c.created_at) AS followup_time,
            DATEDIFF(cp.success_date, cp.first_discuss_date) AS conversion_time,
            (SELECT GROUP_CONCAT(CONCAT(squ.first_name, " ", squ.last_name)) FROM tbl_client_mentor sqcm
                    LEFT JOIN users squ ON squ.id = sqcm.user_id
                    WHERE sqcm.clientprog_id = cp.clientprog_id GROUP BY sqcm.clientprog_id) as mentor_tutor_name        
        FROM tbl_client_prog cp
            LEFT JOIN program p
                ON p.prog_id = cp.prog_id
            LEFT JOIN tbl_client c
                ON c.id = cp.client_id
                    LEFT JOIN tbl_sch sch
                        ON sch.sch_id = c.sch_id
                    LEFT JOIN tbl_lead cl
                        ON cl.lead_id = c.lead_id
                            LEFT JOIN tbl_eduf_lead cedl
                                ON cedl.id = c.eduf_id
                            LEFT JOIN tbl_events cec
                                ON cec.event_id = c.event_id
            LEFT JOIN tbl_client_relation cr
                ON cr.child_id = c.id
            LEFT JOIN tbl_client parent
                ON parent.id = cr.parent_id
            LEFT JOIN users u
                ON u.id = cp.empl_id
            LEFT JOIN tbl_lead cpl
                ON cpl.lead_id = cp.lead_id
            LEFT JOIN tbl_eduf_lead edl
                ON edl.id = cp.eduf_lead_id
            LEFT JOIN tbl_client_event ce
                ON ce.clientevent_id = cp.clientevent_id
                    LEFT JOIN tbl_events e
                        ON e.event_id = ce.event_id
            LEFT JOIN tbl_corp corp
                ON corp.corp_id = cp.partner_id
            LEFT JOIN tbl_reason r
                ON r.reason_id = cp.reason_id
            
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_program_view');
    }
};
