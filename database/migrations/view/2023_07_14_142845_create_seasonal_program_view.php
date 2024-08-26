<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('seasonal_program_view', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasonal_program_view');
    }
};
