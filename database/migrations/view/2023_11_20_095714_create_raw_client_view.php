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

        CREATE OR REPLACE FUNCTION GetRoleClient ( client_id INTEGER )
        RETURNS INTEGER

            BEGIN
                DECLARE role_id INTEGER DEFAULT NULL; 

                SELECT tbl_client_roles.role_id into role_id FROM tbl_client 
                    LEFT JOIN tbl_client_roles ON tbl_client_roles.client_id = tbl_client.id 
                where tbl_client.id = client_id 
                GROUP by tbl_client.id;

                RETURN role_id;
            END; //

        DELIMITER ;
        ');

        DB::statement('
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetClientSuggestion ( fname VARCHAR(50), mname VARCHAR(50), lname VARCHAR(50), role_id INTEGER)
        RETURNS TEXT

            BEGIN
                DECLARE id_similiar TEXT DEFAULT NULL; 

                SELECT GROUP_CONCAT(DISTINCT(tbl_client.id)) INTO id_similiar
                    from tbl_client
                    LEFT JOIN tbl_client_roles ON tbl_client_roles.client_id = tbl_client.id  
                    WHERE (tbl_client.is_verified = "Y"
                        AND tbl_client_roles.role_id = role_id) 
                        AND (first_name like fname COLLATE utf8mb4_unicode_ci
                        OR first_name like mname COLLATE utf8mb4_unicode_ci
                        OR first_name like lname COLLATE utf8mb4_unicode_ci
                        OR last_name like fname COLLATE utf8mb4_unicode_ci
                        OR last_name like mname COLLATE utf8mb4_unicode_ci
                        OR last_name like lname COLLATE utf8mb4_unicode_ci);

                RETURN id_similiar;
            END; //

        DELIMITER ;
        ');

        DB::statement('
        DELIMITER //

        CREATE OR REPLACE FUNCTION CountRawClientRelation ( raw_client_id INTEGER, roles VARCHAR(50) )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_relation INTEGER DEFAULT 0;

            IF roles = "student" OR roles = "parent" THEN

                SELECT COUNT(*) INTO counted_relation FROM tbl_client_relation cr

                        WHERE (CASE 
                                    WHEN roles = "student" THEN child_id
                                    WHEN roles = "parent" THEN parent_id
                                END) = raw_client_id;
                                
                    RETURN counted_relation ;
            ELSE
                RETURN 0;
            END IF;
        END; //
        DELIMITER ;
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
