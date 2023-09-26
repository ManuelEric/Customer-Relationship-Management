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
        CREATE OR REPLACE VIEW client_lead AS
        SELECT 
            cl.id,
            cl.graduation_year,
            CONCAT(cl.first_name, ' ', COALESCE(cl.last_name, '')) as name,
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
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
                Schema::dropIfExists('client_lead_view');
        }
};
