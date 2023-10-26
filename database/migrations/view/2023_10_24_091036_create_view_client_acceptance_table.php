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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_acceptance');
    }
};
