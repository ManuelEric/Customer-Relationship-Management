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

        CREATE OR REPLACE FUNCTION CountSuggest ( fname VARCHAR(50), mname VARCHAR(50), lname VARCHAR(50) )
        RETURNS INTEGER

            BEGIN
                DECLARE count_suggest INTEGER DEFAULT 0; 

                SELECT COUNT(*) INTO count_suggest from tbl_client
                    WHERE first_name like "%" + fname + "%"
                        OR first_name like "%" + mname + "%"
                        OR first_name like "%" + lname + "%"
                        OR last_name like "%" + fname + "%"
                        OR last_name like "%" + mname + "%"
                        OR last_name like "%" + lname + "%";

                RETURN count_suggest;
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
            countSuggest ((select fname), (select mname), (select lname)) as suggest,
            rc.mail,
            rc.phone,
            parent.is_verified as is_verifiedparent,
            parent.id as parent_id,
            CONCAT(parent.first_name, " ", COALESCE(parent.last_name, "")) as parent_name,
            parent.mail as parent_mail,
            parent.phone as parent_phone,
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
            rc.updated_at
            
        
        FROM tbl_client rc
            LEFT JOIN tbl_client_relation cr
                ON cr.child_id = rc.id
            LEFT JOIN tbl_client parent
                ON parent.id = cr.parent_id
            LEFT JOIN tbl_lead l
                ON l.lead_id = rc.lead_id
            LEFT JOIN tbl_sch sch
                ON sch.sch_id = rc.sch_id

        WHERE rc.is_verified = "N" AND rc.deleted_at is null
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
