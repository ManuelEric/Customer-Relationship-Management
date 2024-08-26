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
        # function to get monthly target by requested month & year
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetMonthlyTarget ( requested_month INTEGER, requested_year INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE total INTEGER;

                SELECT SUM(total_participant) INTO total 
                    FROM tbl_sales_target
                    LEFT JOIN tbl_prog ON tbl_prog.prog_id = tbl_sales_target.prog_id
                    LEFT JOIN tbl_main_prog ON tbl_main_prog.id = tbl_prog.main_prog_id
                    LEFT JOIN tbl_main_prog mp2 on mp2.id = tbl_sales_target.main_prog_id
                    WHERE MONTH(tbl_sales_target.month_year) = requested_month AND YEAR(tbl_sales_target.month_year) = requested_year AND (tbl_main_prog.id = 1 OR mp2.id = 1);
            RETURN total;
        END; //

        DELIMITER ;
        ");

        # function to get number 'contribution to target'
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION SetContributionToTarget ( percent DOUBLE, total_target INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE contribution_to_target INTEGER;

                SET contribution_to_target = total_target * (percent/100);

            RETURN contribution_to_target;
        END; //

        DELIMITER ;
        ");

        # function to get initial consult by total target
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION SetInitialConsult ( contribution_to_target INTEGER, requested_division VARCHAR(20) COLLATE utf8mb4_general_ci )
        RETURNS INTEGER

            BEGIN
                DECLARE initial_consult_target INTEGER;
                DECLARE gap_from_lastmonth INTEGER;

                SET gap_from_lastmonth = ABS(IFNULL(GetDiffFromLastMonth(requested_division), 0));

                SET initial_consult_target = (contribution_to_target + gap_from_lastmonth) * 1.5;

            RETURN initial_consult_target;
        END; //

        DELIMITER ;
        ");

        # function to get number of hot leads per division.
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION SetHotLeadsByDivision ( initial_consult_target INTEGER, division VARCHAR(20) )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE hot_leads_target INTEGER;
                DECLARE multiplier INTEGER;

                SET multiplier = 
                    CASE 
                        WHEN division = 'Sales' THEN 2
                        WHEN division = 'Referral' THEN 1
                        WHEN division = 'Digital' THEN 3
                    END;

                SET hot_leads_target = initial_consult_target * multiplier;

            RETURN hot_leads_target;
        END; //

        DELIMITER ;
        ");

        # function to get leads needed by division 
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION SetLeadsNeededByDivision ( hot_leads_target INTEGER, division VARCHAR(20) )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE leads_needed INTEGER;
                DECLARE multiplier INTEGER;

                SET multiplier = 
                    CASE 
                        WHEN division = 'Sales' THEN 2
                        WHEN division = 'Referral' THEN 1
                        WHEN division = 'Digital' THEN 5
                    END;

                SET leads_needed = hot_leads_target * multiplier;

            RETURN leads_needed;
        END; //

        DELIMITER ;
        ");

        # function to get diff from last month
        # example : target 18 but actually only get 17, then the difference should be added to the target for the next month
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetDiffFromLastMonth( requested_division VARCHAR(20) )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE difference INTEGER;

                SELECT contribution_target - contribution_achieved INTO difference
                    FROM target_tracking
                    WHERE MONTH(month_year) = MONTH(now() - INTERVAL 1 MONTH) 
                        AND YEAR(month_year) = YEAR(now() - INTERVAL 1 MONTH)
                        AND divisi = requested_division COLLATE utf8mb4_unicode_ci;

            RETURN difference;
        END; //

        DELIMITER ;
        ");

        # function to get revenue target by requested month & year
        DB::statement("
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetRevenueTarget ( requested_month INTEGER, requested_year INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE total INTEGER;

                SELECT SUM(total_target) INTO total 
                    FROM tbl_sales_target
                    LEFT JOIN tbl_prog ON tbl_prog.prog_id = tbl_sales_target.prog_id
                    LEFT JOIN tbl_main_prog ON tbl_main_prog.id = tbl_prog.main_prog_id
                    LEFT JOIN tbl_main_prog mp2 on mp2.id = tbl_sales_target.main_prog_id
                    WHERE MONTH(tbl_sales_target.month_year) = requested_month AND YEAR(tbl_sales_target.month_year) = requested_year AND (tbl_main_prog.id = 1 OR mp2.id = 1);
            RETURN total;
        END; //

        DELIMITER ;
        ");

        //! find a way to simplify the function per column
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
};
