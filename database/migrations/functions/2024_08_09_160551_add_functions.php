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
        /**
         * Functions need to be installed before using
         * 
         * A. Create Get Client Type Function
         *  a.1. CountJoinAdmissionProgramByClientId
         *  a.2. CountJoinNonAdmissionProgramByClientId
         *  a.3. GetClientType
         * 
         * B. Update Grade Student Function
         * 
         * C. Check Participated Client Function
         *  c.1. CheckParticipatedProgram
         *  c.2. CheckParticipatedEvent
         *  c.3. CheckParticipated
         * 
         * D. Get Grade Student By Graduation Year Function
         * 
         * E. Get Graduation Year Real Function
         * 
         * F. Get Referral Name By Ref Code Function
         * 
         * G. Get Total Score Function. Used to create a client student view
         * 
         * H. Get Monthly Target. Used to create a lead signal view
         * 
         * I. Get initial consult by total target. Used to create a lead signal view
         * 
         * J. Get number of hot leads per division. Used to create a lead signal view
         * 
         * K. Get leads needed by division. Used to create a lead signal view
         * 
         * L. Get diff from last month. Used to create a lead signal view
         * 
         * M. Get revenue target by requested month & year. Used to create a lead signal view
         * 
         * N. Create Referral Code Function. Used to create a client ref code view
         * 
         * O. Get Role Client By Client Id. 
         * 
         * P. Get Client Suggestions based on similarity of first name and last name
         * 
         * Q. Count Client Raw Client Relation
         * 
         */
        DB::statement('
        # a.1
        DELIMITER //

        CREATE OR REPLACE FUNCTION CountJoinAdmissionProgramByClientId ( requested_client_id INTEGER )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_program INTEGER DEFAULT 0;

            SELECT COUNT(*) INTO counted_program FROM tbl_client_prog cp
                    JOIN tbl_prog p ON p.prog_id = cp.prog_id
                        JOIN tbl_main_prog mp ON mp.id = p.main_prog_id
                        JOIN tbl_sub_prog sp ON sp.id = p.sub_prog_id
                    WHERE cp.client_id = requested_client_id
                        AND mp.prog_name = "Admissions Mentoring" AND cp.status = 1;
                   RETURN counted_program;
        END; //
        DELIMITER ;

        # a.2
        DELIMITER //

        CREATE OR REPLACE FUNCTION CountJoinNonAdmissionProgramByClientId ( requested_client_id INTEGER )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_program INTEGER DEFAULT 0;

            SELECT COUNT(*) INTO counted_program FROM tbl_client_prog cp
                    JOIN tbl_prog p ON p.prog_id = cp.prog_id
                        JOIN tbl_main_prog mp ON mp.id = p.main_prog_id
                        JOIN tbl_sub_prog sp ON sp.id = p.sub_prog_id
                    WHERE cp.client_id = requested_client_id
                        AND mp.prog_name != "Admissions Mentoring" AND cp.status = 1;
                   RETURN counted_program;
        END; //
        DELIMITER ;

        # a.3
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetClientType ( requested_client_id INTEGER )
        RETURNS VARCHAR(20)

            BEGIN
                DECLARE client_type VARCHAR(20);
                DECLARE join_program_admission INTEGER DEFAULT 0; 
                DECLARE join_program_non_admission INTEGER DEFAULT 0;

                SET join_program_admission = CountJoinAdmissionProgramByClientId(requested_client_id);
                SET join_program_non_admission = CountJoinNonAdmissionProgramByClientId(requested_client_id);

                IF join_program_admission > 0 THEN
                    SET client_type = "existing_mentee";
                ELSEIF join_program_non_admission > 0 THEN
                    SET client_type = "existing_non_mentee";
                ELSE
                    SET client_type = "new";
                END IF;

                RETURN client_type;
        END; //
        DELIMITER ;

        # B 
        DELIMITER //

        CREATE OR REPLACE FUNCTION UpdateGradeStudent ( ynow INTEGER, yinput INTEGER, mnow INTEGER, minput INTEGER, ginput INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE gradeNow INTEGER;

                IF (mnow >= 7 AND minput < 7) AND (ynow > yinput) THEN
                    SET gradeNow = (ynow - yinput) + (ginput + 1); 
                ELSEIF (mnow < 7 AND minput >= 7) AND (ynow > yinput) THEN
                    SET gradeNow = (ynow - yinput) + (ginput - 1);
                ELSEIF (mnow >= 7 AND minput < 7) AND (ynow = yinput) THEN
                    SET gradeNow = ginput + 1;  
                ELSEIF (mnow < 7 AND minput >= 7) AND (ynow = yinput) THEN
                    SET gradeNow = (ynow - yinput) + (ginput - 1);  
                ELSEIF ((mnow < 7 AND minput < 7) OR (mnow >= 7 AND minput >= 7)) AND (ynow >= yinput) THEN
                    SET gradeNow = (ynow - yinput) + ginput;
                ELSE 
                    SET gradeNow = ginput;  
                END IF;

            RETURN gradeNow;
        END; //
        DELIMITER ;

        # c.1
        DELIMITER //

        CREATE OR REPLACE FUNCTION checkParticipatedProgram ( requested_client_id INTEGER )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_program INTEGER DEFAULT 0;
            SELECT COUNT(*) INTO counted_program FROM tbl_client_prog cp
                    WHERE cp.client_id = requested_client_id;
                   RETURN counted_program;
        END; //
        DELIMITER ;

        # c.2.
        DELIMITER //

        CREATE OR REPLACE FUNCTION checkParticipatedEvent ( requested_client_id INTEGER )
        RETURNS INTEGER
        
        BEGIN
        	DECLARE counted_event INTEGER DEFAULT 0;
            SELECT COUNT(*) INTO counted_event FROM tbl_client_event ce
                
                    WHERE ce.client_id = requested_client_id;
                   RETURN counted_event;
        END; //
        DELIMITER ;

        # c.3.
        DELIMITER //

        CREATE OR REPLACE FUNCTION checkParticipated ( requested_client_id INTEGER )
        RETURNS VARCHAR(10)

            BEGIN
                DECLARE participated VARCHAR(20) COLLATE utf8mb4_general_ci;
                DECLARE join_program INTEGER DEFAULT 0; 
                DECLARE join_event INTEGER DEFAULT 0;

                SET join_program = checkParticipatedProgram(requested_client_id);
                SET join_event = checkParticipatedEvent(requested_client_id);

                IF join_program > 0 OR join_event > 1 THEN
                    SET participated = "Yes";
                ELSE
                    SET participated = "No";
                END IF;

                RETURN participated;
            END; //
        DELIMITER ;

        # D
        DELIMITER //

        CREATE OR REPLACE FUNCTION getGradeStudentByGraduationYear ( graduation_year INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE grade INTEGER;
                DECLARE month_now INTEGER;
                DECLARE diff_year INTEGER;
                
                SET diff_year = graduation_year - YEAR(NOW());
                SET grade = 12 - diff_year;
                SET month_now = MONTH(now());

                IF (month_now >= 7) THEN 
                    SET grade = grade + 1;
                END IF;

            RETURN grade;
        END; //
        DELIMITER ;

        # E
        DELIMITER //

        CREATE OR REPLACE FUNCTION getGraduationYearReal ( grade_now INTEGER )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE graduation_year_real INTEGER;
                DECLARE year_now INTEGER;
                DECLARE month_now INTEGER;
                
                SET year_now = YEAR(now());
                SET month_now = MONTH(now());

                IF (month_now >= 7) THEN 
                    SET graduation_year_real = (12 - grade_now) + year_now + 1;
                ELSE
                    SET graduation_year_real = (12 - grade_now) + year_now;
                END IF;

            RETURN graduation_year_real;
        END; //
        DELIMITER ;

        # F
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetReferralNameByRefCode ( refCode VARCHAR(50) )
        RETURNS VARCHAR(255)
        
        BEGIN
        	DECLARE referral_name VARCHAR(255) DEFAULT NULL;

            SELECT full_name INTO referral_name FROM client_ref_code_view cref
                    WHERE cref.ref_code = refCode;
                   RETURN referral_name;
        END; //
        DELIMITER ;

        # G
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

        # H
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

        # I
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
        
        # J
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

        # K
        DELIMITER //

        CREATE OR REPLACE FUNCTION SetLeadsNeededByDivision ( hot_leads_target INTEGER, division VARCHAR(20) )
        RETURNS INTEGER
        DETERMINISTIC

            BEGIN
                DECLARE leads_needed INTEGER;
                DECLARE multiplier INTEGER;

                SET multiplier = 
                    CASE 
                        WHEN division = `Sales` THEN 2
                        WHEN division = `Referral` THEN 1
                        WHEN division = `Digital` THEN 5
                    END;

                SET leads_needed = hot_leads_target * multiplier;

            RETURN leads_needed;
        END; //
        DELIMITER ;

        # L
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

        # M
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

        # N
        DELIMITER //

        CREATE OR REPLACE FUNCTION CreateRefCode ( identifier INTEGER )
        RETURNS VARCHAR(10)
        DETERMINISTIC

            BEGIN
                DECLARE ref_code VARCHAR(10);

                SELECT CONCAT(UPPER(SUBSTR(first_name, 1, 3)), id) INTO ref_code 
                    FROM tbl_client
                    WHERE id = identifier;
            RETURN ref_code;
        END; //
        DELIMITER ;

        # O
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

        # P
        DELIMITER //

        CREATE OR REPLACE FUNCTION GetClientSuggestion ( fname VARCHAR(50), mname VARCHAR(50), lname VARCHAR(50), role_id INTEGER)
        RETURNS TEXT

        BEGIN
            DECLARE id_similiar TEXT DEFAULT NULL;

            IF lname = "" THEN
                SELECT GROUP_CONCAT(DISTINCT(tbl_client.id)) INTO id_similiar
                FROM tbl_client
                LEFT JOIN tbl_client_roles ON tbl_client_roles.client_id = tbl_client.id
                WHERE tbl_client.is_verified = "Y"
                AND tbl_client.deleted_at is null
                AND tbl_client_roles.role_id = role_id
                AND (first_name LIKE fname COLLATE utf8mb4_unicode_ci);
            ELSE
            SELECT GROUP_CONCAT(DISTINCT(tbl_client.id)) INTO id_similiar
                FROM tbl_client
                LEFT JOIN tbl_client_roles ON tbl_client_roles.client_id = tbl_client.id
                WHERE tbl_client.is_verified = "Y"
                AND tbl_client.deleted_at is null
                AND tbl_client_roles.role_id = role_id
                AND (first_name LIKE fname COLLATE utf8mb4_unicode_ci
                    OR first_name LIKE mname COLLATE utf8mb4_unicode_ci
                    OR first_name LIKE lname COLLATE utf8mb4_unicode_ci
                    OR last_name LIKE fname COLLATE utf8mb4_unicode_ci
                    OR last_name LIKE mname COLLATE utf8mb4_unicode_ci
                    OR last_name LIKE lname COLLATE utf8mb4_unicode_ci);
            END IF;

            RETURN id_similiar;
        END; // 
        DELIMITER ;

        # Q
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
