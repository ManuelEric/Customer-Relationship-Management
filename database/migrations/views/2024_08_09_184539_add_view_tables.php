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
        DB::statement("
        CREATE OR REPLACE VIEW agenda AS
        SELECT 
            asp.id as agenda_id,
            asp.sch_prog_id,
            asp.partner_prog_id,
            asp.eduf_id,
            e.event_id,
            e.event_title,
            e.event_description,
            e.event_startdate,
            e.event_enddate,
            cp.pic_name as partner_pic_name,
            cp.pic_phone as partner_pic_phone,
            corp.corp_name,
            sd.schdetail_fullname as school_pic_name,
            sd.schdetail_phone as school_pic_phone,
            s.sch_id as school_id,
            s.sch_name as school_name,
            asp.sch_pic_id,
            asp.univ_pic_id,
            asp.partner_pic_id,
            asp.start_time,
            asp.end_time,
            asp.priority,
            asp.status,
            asp.speaker_type,
            up.name as university_pic_name,
            up.phone as university_pic_phone,
            u.univ_name as university_name,
            CONCAT(rs.first_name, ' ', rs.last_name) as internal_pic,
            p.prog_program as school_program_name,
            mp.prog_name as school_main_program,
            sp.sub_prog_name as school_sub_program,
            p2.prog_program as partner_program_name,
            mp2.prog_name as partner_main_program,
            sp2.sub_prog_name as partner_sub_program
        FROM tbl_agenda_speaker asp
        LEFT JOIN tbl_events e 
            ON e.event_id = asp.event_id
        LEFT JOIN tbl_corp_pic cp 
            ON cp.id = asp.partner_pic_id
                LEFT JOIN tbl_corp corp
                    ON corp.corp_id = cp.corp_id
        LEFT JOIN tbl_schdetail sd
            ON sd.schdetail_id = asp.sch_pic_id
                LEFT JOIN tbl_sch s
                    ON s.sch_id = sd.sch_id
        LEFT JOIN tbl_univ_pic up
            ON up.id = asp.univ_pic_id
                LEFT JOIN tbl_univ u 
                    ON u.univ_id = up.univ_id
        LEFT JOIN users rs
            ON rs.id = asp.empl_id
        LEFT JOIN tbl_sch_prog schprog
            ON schprog.id = asp.sch_prog_id
                LEFT JOIN tbl_prog p
                    ON p.prog_id = schprog.prog_id
                        LEFT JOIN tbl_main_prog mp
                            ON mp.id = p.main_prog_id
                        LEFT JOIN tbl_sub_prog sp
                            ON sp.id = p.sub_prog_id
           LEFT JOIN tbl_partner_prog ptprog
            ON ptprog.id = asp.partner_prog_id
                LEFT JOIN tbl_prog p2
                    ON p2.prog_id = ptprog.prog_id
                        LEFT JOIN tbl_main_prog mp2
                            ON mp2.id = p2.main_prog_id
                           LEFT JOIN tbl_sub_prog sp2
                            ON sp2.id = p2.sub_prog_id
        LEFT JOIN tbl_eduf_lead edl
            ON edl.id = asp.eduf_id
            
        ");

        DB::statement('
        CREATE OR REPLACE VIEW client AS
        SELECT c.*,
            CONCAT (c.first_name, " ", COALESCE(c.last_name, "")) as full_name,
            (CASE 
                WHEN c.referral_code is not null THEN GetReferralNameByRefCode (c.referral_code)
                ELSE NULL
            END) AS referral_name,
            s.sch_name as school_name,
            (CASE 
                WHEN l.main_lead = "KOL" THEN CONCAT("KOL - ", l.sub_lead)
                WHEN l.main_lead = "External Edufair" THEN (CASE WHEN c.eduf_id is not null THEN vel.organizer_name else "External Edufair" END)
                WHEN l.main_lead = "All-In Event" THEN CONCAT("All-In Event - ", ts.event_title)
                ELSE l.main_lead
            END) AS lead_source,
            GetTotalScore (
                s.sch_score, 
                l.score, 
                (CASE
                    WHEN year(CURDATE()) - c.graduation_year = 0 THEN 7
                    WHEN year(CURDATE()) - c.graduation_year = 1 THEN 5
                    WHEN year(CURDATE()) - c.graduation_year = 2 THEN 4
                    WHEN year(CURDATE()) - c.graduation_year = 3 THEN 3
                    ELSE 1 
                END), 
                (SELECT MAX(t.score) FROM tbl_client_abrcountry ab
                    JOIN tbl_tag t ON t.id = ab.tag_id
                    WHERE ab.client_id = c.id
                )
            ) AS total_score,
            UpdateGradeStudent (
                year(CURDATE()),
                year(c.created_at),
                month(CURDATE()),
                month(c.created_at),
                c.st_grade
            ) AS real_grade,
            (CASE
                WHEN (SELECT real_grade IS NULL) AND c.graduation_year IS NOT NULL THEN getGradeStudentByGraduationYear(c.graduation_year)  
                ELSE (SELECT real_grade)
            END) as grade_now,
            (SELECT ((SELECT grade_now) - 12)) AS year_gap,
            (CASE
                WHEN (SELECT real_grade IS NULL) AND c.graduation_year IS NOT NULL THEN c.graduation_year  
                ELSE getGraduationYearReal((SELECT grade_now))
            END) AS graduation_year_real,
            (SELECT YEAR((NOW() - INTERVAL (SELECT year_gap) YEAR) + INTERVAL 1 YEAR)) AS graduation_year_test,
            (SELECT GROUP_CONCAT(squ.univ_name) FROM tbl_dreams_uni sqdu
                    LEFT JOIN tbl_univ squ ON squ.univ_id = sqdu.univ_id
                    WHERE sqdu.client_id = c.id GROUP BY sqdu.client_id) as dream_uni,
            (SELECT GROUP_CONCAT(evt.event_title
                    SEPARATOR ", "
                ) FROM tbl_client_event ce
                    JOIN tbl_events evt ON evt.event_id = ce.event_id
                    WHERE ce.client_id = c.id GROUP BY ce.client_id) as joined_event,
            (SELECT GROUP_CONCAT(DISTINCT CONCAT(sqmp.prog_name COLLATE utf8mb4_general_ci, " - ", sqp.prog_program COLLATE utf8mb4_general_ci) SEPARATOR ", ") COLLATE utf8mb4_general_ci FROM tbl_interest_prog sqip
                    LEFT JOIN tbl_prog sqp ON sqp.prog_id = sqip.prog_id
                    LEFT JOIN tbl_main_prog sqmp ON sqmp.id = sqp.main_prog_id
                    WHERE sqip.client_id = c.id GROUP BY sqip.client_id) as interest_prog,
            (SELECT GROUP_CONCAT(
                        (CASE
                            WHEN sqt.name = "Other" THEN sqac.country_name
                            ELSE sqt.name 
                        END)
                    ) FROM tbl_client_abrcountry sqac
                    JOIN tbl_tag sqt ON sqt.id = sqac.tag_id
                    WHERE sqac.client_id = c.id GROUP BY sqac.client_id) as abr_country,
            (SELECT GROUP_CONCAT(sqm.name) FROM tbl_dreams_major sqdm
                    JOIN tbl_major sqm ON sqm.id = sqdm.major_id
                    WHERE sqdm.client_id = c.id GROUP BY sqdm.client_id) as dream_major,
            (SELECT name FROM tbl_client_lead_tracking clt
                    LEFT JOIN tbl_initial_program_lead ipl ON clt.initialprogram_id = ipl.id
                    WHERE clt.client_id = c.id AND clt.type = "Program" AND clt.status = 1
                    ORDER BY clt.total_result DESC LIMIT 1) as program_suggest,
            (SELECT (CASE 
                        WHEN total_result >= 0.65 THEN "Hot"
                        WHEN total_result >= 0.35 AND total_result < 0.65 THEN "Warm"
                        WHEN total_result < 0.35 THEN "Cold"
                    END)
                        FROM tbl_client_lead_tracking clt2
                    LEFT JOIN tbl_initial_program_lead ipl2 ON clt2.initialprogram_id = ipl2.id
                    WHERE clt2.client_id = c.id AND clt2.type = "Lead" AND ipl2.name = program_suggest AND clt2.status = 1
                    ORDER BY clt2.total_result DESC LIMIT 1) as status_lead,
            (CASE 
                WHEN
                    (SELECT total_result FROM tbl_client_lead_tracking clt2
                        LEFT JOIN tbl_initial_program_lead ipl2 ON clt2.initialprogram_id = ipl2.id
                        WHERE clt2.client_id = c.id AND clt2.type = "Lead" AND ipl2.name = program_suggest AND clt2.status = 1
                        ORDER BY clt2.total_result DESC LIMIT 1) IS NULL THEN 0
                ELSE 
                (SELECT total_result FROM tbl_client_lead_tracking clt2
                    LEFT JOIN tbl_initial_program_lead ipl2 ON clt2.initialprogram_id = ipl2.id
                    WHERE clt2.client_id = c.id AND clt2.type = "Lead" AND ipl2.name = program_suggest AND clt2.status = 1
                    ORDER BY clt2.total_result DESC LIMIT 1)
            END) AS status_lead_score,
            (SELECT group_id FROM tbl_client_lead_tracking clt3
                    WHERE clt3.client_id = c.id AND clt3.type = "Program" AND clt3.status = 1
                    ORDER BY clt3.total_result DESC LIMIT 1) as group_id,
            (SELECT CONVERT(checkParticipated (c.id) USING utf8mb4)) AS participated,
            (SELECT pic.user_id 
                        FROM tbl_pic_client pic
                    LEFT JOIN users u on u.id = pic.user_id
                    WHERE pic.client_id = c.id AND pic.status = 1 LIMIT 1)
             as pic_id,
            (SELECT CONCAT (u.first_name, " ", COALESCE(u.last_name, "")) 
                        FROM tbl_pic_client pic
                    LEFT JOIN users u on u.id = pic.user_id
                    WHERE pic.client_id = c.id AND pic.status = 1 LIMIT 1)
             as pic_name
            
        
        FROM tbl_client c
            LEFT JOIN tbl_sch s
                ON s.sch_id = c.sch_id
            LEFT JOIN tbl_lead l
                ON l.lead_id = c.lead_id
            LEFT JOIN tbl_eduf_lead el
                ON el.id = c.eduf_id
            LEFT JOIN eduf_lead vel 
                ON vel.id = el.id
            LEFT JOIN tbl_events ts
                ON ts.event_id = c.event_id
        ');

        DB::statement("
        CREATE OR REPLACE VIEW program AS
        SELECT 
            pr.prog_id as prog_id,
            pr.main_prog_id as main_prog_id,
            pr.sub_prog_id as sub_prog_id,
            pr.prog_type,
            pr.prog_mentor,
            pr.prog_payment,
            pr.prog_scope,
            pr.prog_program as prog_program,
            mp.prog_name as main_prog_name,
            sp.sub_prog_name as sub_prog_name,
            pr.active,
            pr.created_at,
            (CASE WHEN pr.sub_prog_id > 0 THEN
                (CASE WHEN mp.prog_name = sp.sub_prog_name THEN
                    CONCAT(mp.prog_name, ' : ', pr.prog_program)
                ELSE 
                    CONCAT(mp.prog_name, ' / ', sp.sub_prog_name, ' : ', pr.prog_program) 
                END)
            ELSE
                CONCAT(mp.prog_name, ' : ', pr.prog_program)
            END) as program_name

        FROM tbl_prog pr
        LEFT JOIN tbl_main_prog mp 
            ON mp.id = pr.main_prog_id
            LEFT JOIN tbl_sub_prog sp 
                ON sp.id = pr.sub_prog_id 
        ");

        DB::statement('
        CREATE OR REPLACE VIEW clientprogram AS
        SELECT cp.*,
            (SELECT pic.user_id 
                        FROM tbl_pic_client pic
                    LEFT JOIN users u on u.id = pic.user_id
                    WHERE pic.client_id = c.id AND pic.status = 1)
             as pic_client,
            (CASE 
                WHEN cp.referral_code is not null THEN GetReferralNameByRefCode (cp.referral_code)
                ELSE NULL
            END) AS referral_name,
            c.st_grade,
            c.register_as,
            UpdateGradeStudent (
                year(CURDATE()),
                year(c.created_at),
                month(CURDATE()),
                month(c.created_at),
                c.st_grade
            ) AS grade_now,
            r.reason_name as reason,
            CONCAT(c.first_name, " ", COALESCE(c.last_name, "")) as fullname,
            c.phone as student_phone,
            c.mail as student_mail,
            sch.sch_name as school_name,
            sch.sch_id,
            CONCAT(parent.first_name, " ", COALESCE(parent.last_name, "")) as parent_fullname,
            parent.phone as parent_phone,
            parent.mail as parent_mail,
            p.main_prog_id,
            p.main_prog_name,
            p.program_name,
            (CASE WHEN cp.status = 0 THEN "Pending"
                WHEN cp.status = 1 THEN "Success"
                WHEN cp.status = 2 THEN "Failed"
                WHEN cp.status = 3 THEN "Refund"
            END) AS program_status,
            CONCAT (u.first_name, " ", COALESCE(u.last_name, "")) AS pic_name,
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
                WHEN cpl.main_lead = "KOL" THEN CONCAT("KOL - ", cpl.sub_lead)
                WHEN cpl.main_lead = "External Edufair" THEN CONCAT("External Edufair - ", edl.title)
                WHEN cpl.main_lead = "All-In Event" THEN CONCAT("All-In Event - ", e.event_title)
                WHEN cpl.main_lead = "All-In Partners" THEN CONCAT("All-In Partner - ", corp.corp_name)
                ELSE cpl.main_lead
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


                GROUP BY cp.clientprog_id;
        ');

        DB::statement("
        CREATE OR REPLACE VIEW client_lead AS
        SELECT 
            cl.id,
            cl.graduation_year,
            CONCAT(cl.first_name, ' ', COALESCE(cl.last_name, '')) as name,
            cl.phone,
            UpdateGradeStudent (
                year(CURDATE()),
                year(cl.created_at),
                month(CURDATE()),
                month(cl.created_at),
                cl.st_grade
            ) AS real_grade,
            (CASE
                WHEN (SELECT real_grade) IS NULL AND cl.graduation_year IS NOT NULL THEN getGradeStudentByGraduationYear(cl.graduation_year)
                ELSE (SELECT real_grade)
            END) -12 AS grade_client_lead,
            (SELECT grade_client_lead) AS grade,
            sc.sch_id as school,
            sc.sch_type as type_school,
            (CASE 
                WHEN l.main_lead = 'Referral' THEN 'Referral'
                ELSE 'Other'
            END) AS lead_source,
            cl.is_funding,
            (SELECT GROUP_CONCAT(sqt.name ORDER BY FIELD(name, 'US','UK','Canada','Australia','Other','Asia')) FROM tbl_client_abrcountry sqac
                    JOIN tbl_tag sqt ON sqt.id = sqac.tag_id
                    WHERE sqac.client_id = cl.id GROUP BY sqac.client_id) as interested_country,
            (SELECT GROUP_CONCAT(sqm.name) FROM tbl_dreams_major sqdm
                    JOIN tbl_major sqm ON sqm.id = sqdm.major_id
                    WHERE sqdm.client_id = cl.id GROUP BY sqdm.client_id) as major,

            (SELECT id FROM tbl_school_categorization_lead schctg
                    WHERE schctg.value COLLATE utf8mb4_unicode_ci = sc.sch_type COLLATE utf8mb4_unicode_ci) as school_categorization,
            (SELECT id FROM tbl_grade_categorization_lead grdctg
                    WHERE grdctg.value = grade) as grade_categorization,
                (CASE
                        WHEN (SELECT interested_country) IS NULL AND cl.is_funding != 1 THEN 8
                        WHEN (SELECT interested_country) IS NULL AND cl.is_funding = 1 THEN 9
                        ELSE 
                        (SELECT id FROM tbl_country_categorization_lead ctyctg
                        WHERE substring_index(substring_index(interested_country, ',', 1), ',', -1) = ctyctg.value)

                END) AS country_categorization,
            
            (SELECT id FROM tbl_major_categorization_lead mjrctg
                    WHERE mjrctg.value = (CASE major WHEN major is null THEN 'Decided' ELSE 'Undecided' END)) as major_categorization,
            
            (SELECT GROUP_CONCAT(role_name) FROM tbl_client_roles clrole
                    JOIN tbl_roles role ON role.id = clrole.role_id
                    WHERE clrole.client_id = cl.id) as roles,

            GetClientType(cl.id) as type,
            cl.register_as as register_as,
            cl.st_statusact as active

        FROM tbl_client cl
        LEFT JOIN client cv
                ON cv.id = cl.id
        LEFT JOIN tbl_sch sc 
            ON sc.sch_id = cl.sch_id
        LEFT JOIN tbl_lead l
            ON l.lead_id = cl.lead_id

            WHERE (
                SELECT GROUP_CONCAT(role_name) FROM tbl_client_roles clrole
                        JOIN tbl_roles role ON role.id = clrole.role_id
                        WHERE clrole.client_id = cl.id
                ) NOT IN ('Parent') 
                AND cl.st_statusact = 1

        ");

        DB::statement("
        CREATE OR REPLACE VIEW client_lead AS
        SELECT 
            cl.id,
            CONCAT(cl.first_name, ' ', COALESCE(cl.last_name, '')) as name,
            cl.st_grade -12 as grade,
            sc.sch_id as school,
            cl.is_funding,
            (SELECT GROUP_CONCAT(sqt.name) FROM tbl_client_abrcountry sqac
                    JOIN tbl_tag sqt ON sqt.id = sqac.tag_id
                    WHERE sqac.client_id = cl.id GROUP BY sqac.client_id) as interested_country,
            (SELECT GROUP_CONCAT(sqm.name) FROM tbl_dreams_major sqdm
                    JOIN tbl_major sqm ON sqm.id = sqdm.major_id
                    WHERE sqdm.client_id = cl.id GROUP BY sqdm.client_id) as major,

            (SELECT id FROM tbl_school_categorization_lead schctg
                    WHERE schctg.value COLLATE utf8mb4_unicode_ci = sc.sch_type COLLATE utf8mb4_unicode_ci) as school_categorization,
            (SELECT id FROM tbl_grade_categorization_lead grdctg
                    WHERE grdctg.value = grade) as grade_categorization,
            (SELECT id FROM tbl_country_categorization_lead ctyctg
                    WHERE substring_index(substring_index(interested_country, ',', 1), ',', -1) = ctyctg.value) as country_categorization,
            (SELECT id FROM tbl_major_categorization_lead mjrctg
                    WHERE mjrctg.value = (CASE major WHEN major is null THEN 'Decided' ELSE 'Undecided' END)) as major_categorization,
            
            (SELECT GROUP_CONCAT(role_name) FROM tbl_client_roles clrole
                    JOIN tbl_roles role ON role.id = clrole.role_id
                    WHERE clrole.client_id = cl.id) as roles,

            GetClientType(cl.id) as type

        FROM tbl_client cl
        LEFT JOIN tbl_sch sc 
            ON sc.sch_id = cl.sch_id

            WHERE (SELECT GROUP_CONCAT(role_name) FROM tbl_client_roles clrole
            JOIN tbl_roles role ON role.id = clrole.role_id
            WHERE clrole.client_id = cl.id) NOT IN ('Parent')

        ");

        DB::statement('
        CREATE OR REPLACE VIEW target_signal_view AS
            SELECT 
                id,
                divisi,
                contribution_in_percent,
                GetMonthlyTarget(MONTH(now()), YEAR(now())) as monthly_target,
                SetContributionToTarget(contribution_in_percent, (SELECT monthly_target)) as contribution_to_target,
                SetInitialConsult(SetContributionToTarget(contribution_in_percent, (SELECT monthly_target)), divisi) as initial_consult_target,
                SetHotLeadsByDivision(SetInitialConsult(SetContributionToTarget(contribution_in_percent, (SELECT monthly_target)), divisi), divisi) as hot_leads_target,
                SetLeadsNeededByDivision(SetHotLeadsByDivision(SetInitialConsult(SetContributionToTarget(contribution_in_percent, (SELECT monthly_target)), divisi), divisi), divisi) as lead_needed,
                GetRevenueTarget(MONTH(now()), YEAR(now())) as revenue_target
                
            FROM contribution_calculation_tmp 
        ');

        DB::statement('
        CREATE OR REPLACE VIEW client_ref_code_view AS
            SELECT id, first_name, last_name, CONCAT (first_name, " ", COALESCE(last_name, "")) as full_name, CreateRefCode(id) as ref_code COLLATE utf8mb4_unicode_ci
            FROM tbl_client;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW client_acceptance AS
        SELECT 
            ac.id,
            c.id as client_id,
            CONCAT(c.first_name, " ", COALESCE(c.last_name, "")) as full_name,
            c.graduation_year,
            (SELECT GROUP_CONCAT(CONCAT("[", u.univ_name, " - ", m.name, "]") SEPARATOR ", ") COLLATE utf8mb4_general_ci FROM tbl_client_acceptance sac
                LEFT JOIN tbl_major m ON m.id = sac.major_id
                LEFT JOIN tbl_univ u ON u.univ_id = sac.univ_id
                WHERE sac.client_id = ac.client_id AND sac.status = "waitlisted") as waitlisted_groups,
            (SELECT GROUP_CONCAT(CONCAT("[", u.univ_name, " - ", m.name, "]") SEPARATOR ", ") COLLATE utf8mb4_general_ci FROM tbl_client_acceptance sac
                LEFT JOIN tbl_major m ON m.id = sac.major_id
                LEFT JOIN tbl_univ u ON u.univ_id = sac.univ_id
                WHERE sac.client_id = ac.client_id AND sac.status = "accepted") as accepted_groups,
            (SELECT GROUP_CONCAT(CONCAT("[", u.univ_name, " - ", m.name, "]") SEPARATOR ", ") COLLATE utf8mb4_general_ci FROM tbl_client_acceptance sac
                LEFT JOIN tbl_major m ON m.id = sac.major_id
                LEFT JOIN tbl_univ u ON u.univ_id = sac.univ_id
                WHERE sac.client_id = ac.client_id AND sac.status = "denied") as denied_groups,
            (SELECT GROUP_CONCAT(CONCAT("[", u.univ_name, " - ", m.name, "]") SEPARATOR ", ") COLLATE utf8mb4_general_ci FROM tbl_client_acceptance sac
                LEFT JOIN tbl_major m ON m.id = sac.major_id
                LEFT JOIN tbl_univ u ON u.univ_id = sac.univ_id
                WHERE sac.client_id = ac.client_id AND sac.status = "chosen") as chosen_groups
        FROM tbl_client_acceptance ac
            LEFT JOIN tbl_client c ON c.id = ac.client_id
        GROUP BY ac.client_id;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW outstanding_payment_view AS
        SELECT 
            i.id,
            i.inv_id as invoice_id,
            SUBSTRING_INDEX(SUBSTRING_INDEX(i.inv_id, "/", 1), "/", -1) as inv_id_num,
            SUBSTRING_INDEX(SUBSTRING_INDEX(i.inv_id, "/", 4), "/", -1) as inv_id_month,
            SUBSTRING_INDEX(SUBSTRING_INDEX(i.inv_id, "/", 5), "/", -1) as inv_id_year,
            CONCAT(ic.first_name, " ", COALESCE(ic.last_name, "")) as full_name,
            i.clientprog_id as client_prog_id,
            ip.program_name,
            id.invdtl_installment as installment_name,
            "B2C" as type,
            (CASE
                WHEN i.inv_paymentmethod = "Full Payment" THEN 
                    i.inv_totalprice_idr 
                WHEN i.inv_paymentmethod = "Installment" THEN 
                    id.invdtl_amountidr
                ELSE null
            END) as total,
            (CASE 
                WHEN i.inv_paymentmethod = "Full Payment" THEN 
                    i.inv_duedate 
                WHEN i.inv_paymentmethod = "Installment" THEN 
                    id.invdtl_duedate
            END) as invoice_duedate,
            i.clientprog_id,
            ic.id as client_id,
            ic.phone as child_phone,
            ipr.phone as parent_phone,
            CONCAT(ipr.first_name, " ", COALESCE(ipr.last_name, "")) as parent_name,
            ipr.id as parent_id,
            "client_prog" as typeprog,
            i.inv_paymentmethod as payment_method
                FROM tbl_inv i
                LEFT JOIN tbl_invdtl id ON i.inv_id = id.inv_id 
                LEFT JOIN tbl_receipt ir ON 
                    (CASE
                        WHEN i.inv_paymentmethod = "Full Payment" THEN 
                            ir.inv_id 
                        WHEN i.inv_paymentmethod = "Installment" THEN 
                                ir.invdtl_id
                        ELSE null
                    END) = (CASE
                        WHEN i.inv_paymentmethod = "Full Payment" THEN 
                            i.inv_id 
                        WHEN i.inv_paymentmethod = "Installment" THEN 
                                id.invdtl_id
                        ELSE null
                    END)
                LEFT JOIN tbl_client_prog icp ON icp.clientprog_id = i.clientprog_id
                LEFT JOIN program ip ON ip.prog_id = icp.prog_id
                LEFT JOIN tbl_client ic ON ic.id = icp.client_id
                LEFT JOIN tbl_client_relation icr ON icr.child_id = ic.id
                LEFT JOIN tbl_client ipr ON ipr.id = icr.parent_id
            WHERE icp.status = 1
        UNION
            SELECT
                ib2b.invb2b_num as id,
                ib2b.invb2b_id as invoice_id,
                SUBSTRING_INDEX(SUBSTRING_INDEX(ib2b.invb2b_id, "/", 1), "/", -1) as inv_id_num,
                SUBSTRING_INDEX(SUBSTRING_INDEX(ib2b.invb2b_id, "/", 4), "/", -1) as inv_id_month,
                SUBSTRING_INDEX(SUBSTRING_INDEX(ib2b.invb2b_id, "/", 5), "/", -1) as inv_id_year,
                (CASE 
                    WHEN ib2b.schprog_id > 0 THEN ib2bs.sch_name
                    WHEN ib2b.partnerprog_id > 0 THEN ib2bc.corp_name
                    WHEN ib2b.ref_id > 0 THEN ib2bc.corp_name
                    ELSE NULL
                END) COLLATE utf8mb4_general_ci as full_name,
                (CASE 
                    WHEN ib2b.schprog_id > 0 THEN ib2b.schprog_id
                    WHEN ib2b.partnerprog_id > 0 THEN ib2b.partnerprog_id
                    WHEN ib2b.ref_id > 0 THEN ib2b.ref_id
                    ELSE NULL
                END) as client_prog_id,
                (CASE
                    WHEN ib2b.ref_id > 0 THEN ib2brf.additional_prog_name 
                    ELSE
                        ib2bp.program_name
                END) AS program_name,
                ib2bd.invdtl_installment as installment_name,
                "B2B" as type,
                (CASE
                    WHEN ib2b.invb2b_pm = "Full Payment" THEN ib2b.invb2b_totpriceidr 
                    WHEN ib2b.invb2b_pm = "Installment" THEN ib2bd.invdtl_amountidr
                    ELSE null
                END) as total,
                (CASE
                    WHEN ib2b.invb2b_pm = "Full Payment" THEN ib2b.invb2b_duedate
                    WHEN ib2b.invb2b_pm = "Installment" THEN ib2bd.invdtl_duedate
                    ELSE null
                END) as invoice_duedate,
                null as clientprog_id,
                null as client_id,
                null as child_phone,
                null as parent_phone,
                null as parent_name,
                null as parent_id,
                (CASE 
                    WHEN ib2b.schprog_id > 0 THEN "sch_prog"
                    WHEN ib2b.partnerprog_id > 0 THEN "partner_prog"
                    WHEN ib2b.ref_id > 0 THEN "referral"
                    ELSE NULL
                END) as typeprog,
                ib2b.invb2b_pm as payment_method
                    FROM tbl_invb2b ib2b
                    LEFT JOIN tbl_invdtl ib2bd ON ib2bd.invb2b_id = ib2b.invb2b_id
                    LEFT JOIN tbl_receipt ib2br ON 
                        (CASE
                            WHEN ib2b.invb2b_pm = "Full Payment" THEN 
                                ib2br.invb2b_id 
                            WHEN ib2b.invb2b_pm = "Installment" THEN 
                                    ib2br.invdtl_id
                            ELSE null
                        END ) = (CASE
                            WHEN ib2b.invb2b_pm = "Full Payment" THEN 
                                ib2b.invb2b_id 
                            WHEN ib2b.invb2b_pm = "Installment" THEN 
                                    ib2bd.invdtl_id
                            ELSE null
                        END)
                    LEFT JOIN tbl_sch_prog ib2bsp ON ib2bsp.id = ib2b.schprog_id
                    LEFT JOIN tbl_sch ib2bs ON ib2bs.sch_id = ib2bsp.sch_id
                    LEFT JOIN tbl_partner_prog ib2bpp ON ib2bpp.id = ib2b.partnerprog_id
                    LEFT JOIN tbl_referral ib2brf ON ib2brf.id = ib2b.ref_id
                    LEFT JOIN tbl_corp ib2bc ON ib2bc.corp_id = (CASE 
                            WHEN ib2b.partnerprog_id > 0 THEN ib2bpp.corp_id 
                            WHEN ib2b.ref_id > 0 THEN ib2brf.partner_id 
                            ELSE NULL 
                        END)
                    LEFT JOIN program ib2bp ON ib2bp.prog_id = (CASE 
                            WHEN ib2b.schprog_id > 0 THEN ib2bsp.prog_id
                                WHEN ib2b.partnerprog_id > 0 THEN ib2bpp.prog_id
                            ELSE NULL
                        END)
            WHERE ib2br.id IS NULL AND
            (CASE
                WHEN ib2b.schprog_id > 0 THEN ib2bsp.status
                WHEN ib2b.partnerprog_id > 0 THEN ib2bpp.status
                WHEN ib2b.ref_id > 0 THEN ib2brf.referral_type
                ELSE NULL
            END) = (CASE
                WHEN ib2b.ref_id > 0 THEN "Out"
                ELSE 1
            END)
        ORDER BY inv_id_year ASC, inv_id_month ASC, inv_id_num ASC
        ');

        DB::statement('
        CREATE OR REPLACE VIEW raw_client AS
        SELECT 
            rc.id,
            CONCAT(rc.first_name, " ", COALESCE(rc.last_name, "")) as fullname,
            SUBSTRING_INDEX(SUBSTRING_INDEX((SELECT fullname), " ", 1), " ", -1) as fname,
            SUBSTRING_INDEX(SUBSTRING_INDEX((SELECT fullname), " ", 2), " ", -1) as mname,
            SUBSTRING_INDEX(SUBSTRING_INDEX((SELECT fullname), " ", 3), " ", -1) as lname,
            GetClientSuggestion ((select fname), (select mname), (select lname), GetRoleClient(rc.id)) as suggestion,
            rc.mail,
            rc.phone,
            second_client.is_verified as is_verifiedsecond_client,
            second_client.id as second_client_id,
            CONCAT(second_client.first_name, " ", COALESCE(second_client.last_name, "")) as second_client_name,
            second_client.mail as second_client_mail,
            second_client.phone as second_client_phone,
            scsch.sch_name as second_school_name,
            scsch.is_verified as is_verifiedsecond_school,
            rc.scholarship,
            UpdateGradeStudent (
                year(CURDATE()),
                year(rc.created_at),
                month(CURDATE()),
                month(rc.created_at),
                second_client.st_grade
            ) AS second_client_real_grade,
            (CASE
                WHEN (SELECT second_client_real_grade IS NULL) AND second_client.graduation_year IS NOT NULL THEN (12 - (second_client.graduation_year - YEAR(NOW())))  
                ELSE (SELECT second_client_real_grade)
            END) as second_client_grade_now,
            (SELECT ((SELECT second_client_grade_now) - 12)) AS second_client_year_gap,
            (SELECT YEAR((NOW() - INTERVAL (SELECT second_client_year_gap) YEAR) + INTERVAL 1 YEAR)) AS second_client_graduation_year_real,
            (SELECT GROUP_CONCAT(
                (CASE
                    WHEN sqt.name = "Other" THEN sqac.country_name
                    ELSE sqt.name 
                END)
                SEPARATOR ", "
            ) FROM tbl_client_abrcountry sqac
                JOIN tbl_tag sqt ON sqt.id = sqac.tag_id
                WHERE sqac.client_id = second_client.id GROUP BY sqac.client_id) as second_client_interest_countries,
            (SELECT GROUP_CONCAT(evt.event_title
                SEPARATOR ", "
            ) FROM tbl_client_event ce
                JOIN tbl_events evt ON evt.event_id = ce.event_id
                WHERE ce.client_id = second_client.id GROUP BY ce.client_id) as second_client_joined_event,
            (SELECT GROUP_CONCAT(sqp.prog_program) FROM tbl_interest_prog sqip
                LEFT JOIN tbl_prog sqp ON sqp.prog_id = sqip.prog_id
                WHERE sqip.client_id = second_client.id GROUP BY sqip.client_id) as second_client_interest_prog,
            second_client.created_at as second_client_created_at,
            second_client.st_statusact as second_client_statusact,
            UpdateGradeStudent (
                year(CURDATE()),
                year(rc.created_at),
                month(CURDATE()),
                month(rc.created_at),
                rc.st_grade
            ) AS real_grade,
            (CASE
                WHEN (SELECT real_grade IS NULL) AND rc.graduation_year IS NOT NULL THEN (12 - (rc.graduation_year - YEAR(NOW())))  
                ELSE (SELECT real_grade)
            END) as grade_now,
            (SELECT ((SELECT grade_now) - 12)) AS year_gap,
            (SELECT YEAR((NOW() - INTERVAL (SELECT year_gap) YEAR) + INTERVAL 1 YEAR)) AS graduation_year_real,
            rc.graduation_year,
            rc.lead_id,
            (CASE 
                WHEN l.main_lead = "KOL" THEN CONCAT("KOL - ", l.sub_lead)
                ELSE l.main_lead
            END) AS lead_source,
            (CASE 
                WHEN rc.referral_code is not null THEN GetReferralNameByRefCode (rc.referral_code)
                ELSE NUll
            END COLLATE utf8mb4_unicode_ci) AS referral_name,
            sch.sch_id,
            (SELECT GROUP_CONCAT(
                (CASE
                    WHEN sqt.name = "Other" THEN sqac.country_name
                    ELSE sqt.name 
                END)
                SEPARATOR ", "
            ) FROM tbl_client_abrcountry sqac
                JOIN tbl_tag sqt ON sqt.id = sqac.tag_id
                WHERE sqac.client_id = rc.id GROUP BY sqac.client_id) as interest_countries,
            rc.created_at,
            rc.updated_at,
            (SELECT GROUP_CONCAT(sr.role_name SEPARATOR ", ") FROM tbl_client_roles scr
                LEFT JOIN tbl_roles sr ON sr.id = scr.role_id
                WHERE scr.client_id = rc.id) as roles,
            (CASE 
                WHEN (SELECT roles) = "Parent" THEN scsch.sch_name 
                ELSE sch.sch_name
            END) AS school_name,
            (CASE 
                WHEN (SELECT roles) = "Parent" THEN scsch.is_verified 
                ELSE sch.is_verified
            END) AS is_verifiedschool,
            CountRawClientRelation((SELECT rc.id), (SELECT roles)) as count_second_client,
            (SELECT GROUP_CONCAT(evt.event_title
                SEPARATOR ", "
            ) FROM tbl_client_event ce
                JOIN tbl_events evt ON evt.event_id = ce.event_id
                WHERE ce.client_id = rc.id GROUP BY ce.client_id) as joined_event,
            (SELECT GROUP_CONCAT(DISTINCT sqp.prog_program SEPARATOR ", ") FROM tbl_interest_prog sqip
                LEFT JOIN tbl_prog sqp ON sqp.prog_id = sqip.prog_id
                WHERE sqip.client_id = rc.id GROUP BY sqip.client_id) as interest_prog,
            (SELECT pic.user_id 
                    FROM tbl_pic_client pic
                LEFT JOIN users u on u.id = pic.user_id
                WHERE pic.client_id = rc.id AND pic.status = 1)
             as pic
            
            
        FROM tbl_client rc
            INNER JOIN tbl_client_roles crl ON crl.client_id = rc.id
            INNER JOIN tbl_roles rl ON rl.id = crl.role_id

            LEFT JOIN tbl_client_relation cr
                ON (CASE 
                        WHEN rl.role_name = "Student" THEN cr.child_id
                        WHEN rl.role_name = "Parent" THEN cr.parent_id
                    END) = rc.id
            LEFT JOIN tbl_client second_client
                ON second_client.id = (CASE 
                                        WHEN rl.role_name = "Student" THEN cr.parent_id
                                        WHEN rl.role_name = "Parent" THEN cr.child_id
                                    END)
            LEFT JOIN tbl_lead l
                ON l.lead_id = rc.lead_id
            LEFT JOIN tbl_sch sch
                ON sch.sch_id = rc.sch_id
            LEFT JOIN tbl_sch scsch
                ON scsch.sch_id = second_client.sch_id

        WHERE rc.is_verified = "N" AND rc.st_statusact = 1 AND rc.deleted_at is null
        ');

        DB::statement('
        CREATE OR REPLACE VIEW eduf_lead AS
        SELECT 
            edl.id, 
            edl.sch_id,
            edl.corp_id,
            s.sch_name,
            corp.corp_name,
            edl.event_start,
            edl.created_at,
            (CASE 
                WHEN edl.title is not null THEN
                    CONCAT("External Edufair - ", edl.title) 
                WHEN edl.sch_id is not null THEN
                    (CASE WHEN edl.event_start is not null THEN
                        CONCAT(s.sch_name, " (",DATE_FORMAT(edl.event_start, "%d %b %Y"), ")")
                    ELSE 
                        CONCAT(s.sch_name, " (",DATE_FORMAT(edl.created_at, "%d %b %Y"), ")")
                    END)
                WHEN edl.corp_id is not null THEN
                    (CASE WHEN edl.event_start is not null THEN
                        CONCAT(corp.corp_name, " (",DATE_FORMAT(edl.event_start, "%d %b %Y"), ")")
                    ELSE 
                        CONCAT(corp.corp_name, " (",DATE_FORMAT(edl.created_at, "%d %b %Y"), ")")
                    END)
                ELSE
                    "External Edufair"
            END) as organizer_name
        FROM tbl_eduf_lead edl
            LEFT JOIN tbl_sch s
                on s.sch_id = edl.sch_id
            LEFT JOIN tbl_corp corp
                on corp.corp_id = edl.corp_id
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
