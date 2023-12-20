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
                        AND tbl_client_roles.role_id = role_id
                        AND tbl_client.deleted_at is null) 
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
            sch.sch_id,
            sch.sch_name AS school_name,
            sch.is_verified AS is_verifiedschool,
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
                WHERE scr.client_id = rc.id) as roles
            
            
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
