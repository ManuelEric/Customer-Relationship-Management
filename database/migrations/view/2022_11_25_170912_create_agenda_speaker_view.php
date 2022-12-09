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
        CREATE OR REPLACE VIEW Agenda AS
        SELECT 
            asp.id as agenda_id,
            e.event_id,
            asp.sch_prog_id,
            e.event_title,
            e.event_description,
            e.event_startdate,
            e.event_enddate,
            cp.pic_name as partner_pic_name,
            cp.pic_phone as partner_pic_phone,
            corp.corp_name,
            sd.schdetail_fullname as school_pic_name,
            sd.schdetail_phone as school_pic_phone,
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
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agenda_speaker_view');
    }
};
