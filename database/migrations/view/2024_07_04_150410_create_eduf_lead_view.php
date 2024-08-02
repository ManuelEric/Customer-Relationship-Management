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
        Schema::create('eduf_lead_view', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eduf_lead_view');
    }
};
