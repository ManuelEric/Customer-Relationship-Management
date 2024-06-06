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

        CREATE OR REPLACE FUNCTION GetTotalScore ( school_score INTEGER, lead_score INTEGER, school_year_left_score INTEGER, destination_country_score INTEGER )
        RETURNS DECIMAL(4,2)
        DETERMINISTIC

            BEGIN
                DECLARE total DECIMAL(4,2);

                IF destination_country_score IS NULL THEN
                    IF school_score IS NULL THEN 
                        SET total = (lead_score + school_year_left_score) / 2;
                    ELSE
                	    SET total = (school_score + lead_score + school_year_left_score) / 3;
                    END IF;
                ELSE
                	SET total = (school_score + lead_score + school_year_left_score + destination_country_score) / 4;
                END IF;

            RETURN total;
        END; //

        DELIMITER ;
        ');

        DB::statement('
        CREATE OR REPLACE VIEW client AS
        SELECT c.*,
            CONCAT (c.first_name, " ", COALESCE(c.last_name, "")) as full_name,
            (CASE 
                WHEN c.referral_code is not null THEN GetReferralNameByRefCode (c.referral_code)
                ELSE NULL
            END COLLATE utf8mb4_unicode_ci) AS referral_name,
            s.sch_name as school_name,
            (CASE 
                WHEN l.main_lead = "KOL" THEN CONCAT("KOL - ", l.sub_lead)
                WHEN l.main_lead = "External Edufair" THEN CONCAT("External Edufair - ", el.title)
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
            LEFT JOIN tbl_events ts
                ON ts.event_id = c.event_id
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
