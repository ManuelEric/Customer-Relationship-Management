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
            CREATE OR REPLACE VIEW outstanding_payment_view AS
            SELECT 
                i.id,
                i.inv_id as invoice_id,
                SUBSTRING_INDEX(SUBSTRING_INDEX(i.inv_id, "/", 1), "/", -1) as inv_id_num,
                SUBSTRING_INDEX(SUBSTRING_INDEX(i.inv_id, "/", 4), "/", -1) as inv_id_month,
                SUBSTRING_INDEX(SUBSTRING_INDEX(i.inv_id, "/", 5), "/", -1) as inv_id_year,
                CONCAT(ic.first_name, " ", COALESCE(ic.last_name, "")) as full_name,
                i.clientprog_id as client_prog_id,
                ip.program_name,
                id.invdtl_installment as installment_name,
                "B2C" as type,
                (CASE
                    WHEN i.inv_paymentmethod = "Full Payment" THEN 
                        i.inv_totalprice_idr 
                    WHEN i.inv_paymentmethod = "Installment" THEN 
                        id.invdtl_amountidr
                    ELSE null
                END) as total,
                (CASE 
                    WHEN i.inv_paymentmethod = "Full Payment" THEN 
                        i.inv_duedate 
                    WHEN i.inv_paymentmethod = "Installment" THEN 
                        id.invdtl_duedate
                END) as invoice_duedate,
                i.clientprog_id,
                ic.id as client_id,
                ic.phone as child_phone,
                ipr.phone as parent_phone,
                CONCAT(ipr.first_name, " ", COALESCE(ipr.last_name, "")) as parent_name,
                ipr.id as parent_id,
                "client_prog" as typeprog,
                i.inv_paymentmethod as payment_method
                    FROM tbl_inv i
                    LEFT JOIN tbl_invdtl id ON i.inv_id = id.inv_id 
                    LEFT JOIN tbl_receipt ir ON 
                        (CASE
                            WHEN i.inv_paymentmethod = "Full Payment" THEN 
                                ir.inv_id 
                            WHEN i.inv_paymentmethod = "Installment" THEN 
                                    ir.invdtl_id
                            ELSE null
                        END) = (CASE
                            WHEN i.inv_paymentmethod = "Full Payment" THEN 
                                i.inv_id 
                            WHEN i.inv_paymentmethod = "Installment" THEN 
                                    id.invdtl_id
                            ELSE null
                        END)
                    LEFT JOIN tbl_client_prog icp ON icp.clientprog_id = i.clientprog_id
                    LEFT JOIN program ip ON ip.prog_id = icp.prog_id
                    LEFT JOIN tbl_client ic ON ic.uuid = icp.client_uuid
                    LEFT JOIN tbl_client_relation icr ON icr.child_id = ic.id
                    LEFT JOIN tbl_client ipr ON ipr.id = icr.parent_id
                WHERE icp.status = 1
            UNION
                SELECT
                    ib2b.invb2b_num as id,
                    ib2b.invb2b_id as invoice_id,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(ib2b.invb2b_id, "/", 1), "/", -1) as inv_id_num,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(ib2b.invb2b_id, "/", 4), "/", -1) as inv_id_month,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(ib2b.invb2b_id, "/", 5), "/", -1) as inv_id_year,
                    (CASE 
                        WHEN ib2b.schprog_id > 0 THEN ib2bs.sch_name
                        WHEN ib2b.partnerprog_id > 0 THEN ib2bc.corp_name
                        WHEN ib2b.ref_id > 0 THEN ib2bc.corp_name
                        ELSE NULL
                    END) COLLATE utf8mb4_general_ci as full_name,
                    (CASE 
                        WHEN ib2b.schprog_id > 0 THEN ib2b.schprog_id
                        WHEN ib2b.partnerprog_id > 0 THEN ib2b.partnerprog_id
                        WHEN ib2b.ref_id > 0 THEN ib2b.ref_id
                        ELSE NULL
                    END) as client_prog_id,
                    (CASE
                        WHEN ib2b.ref_id > 0 THEN ib2brf.additional_prog_name 
                        ELSE
                            ib2bp.program_name
                    END) AS program_name,
                    ib2bd.invdtl_installment as installment_name,
                    "B2B" as type,
                    (CASE
                        WHEN ib2b.invb2b_pm = "Full Payment" THEN ib2b.invb2b_totpriceidr 
                        WHEN ib2b.invb2b_pm = "Installment" THEN ib2bd.invdtl_amountidr
                        ELSE null
                    END) as total,
                    (CASE
                        WHEN ib2b.invb2b_pm = "Full Payment" THEN ib2b.invb2b_duedate
                        WHEN ib2b.invb2b_pm = "Installment" THEN ib2bd.invdtl_duedate
                        ELSE null
                    END) as invoice_duedate,
                    null as clientprog_id,
                    null as client_id,
                    null as child_phone,
                    null as parent_phone,
                    null as parent_name,
                    null as parent_id,
                    (CASE 
                        WHEN ib2b.schprog_id > 0 THEN "sch_prog"
                        WHEN ib2b.partnerprog_id > 0 THEN "partner_prog"
                        WHEN ib2b.ref_id > 0 THEN "referral"
                        ELSE NULL
                    END) as typeprog,
                    ib2b.invb2b_pm as payment_method
                        FROM tbl_invb2b ib2b
                        LEFT JOIN tbl_invdtl ib2bd ON ib2bd.invb2b_id = ib2b.invb2b_id
                        LEFT JOIN tbl_receipt ib2br ON 
                            (CASE
                                WHEN ib2b.invb2b_pm = "Full Payment" THEN 
                                    ib2br.invb2b_id 
                                WHEN ib2b.invb2b_pm = "Installment" THEN 
                                        ib2br.invdtl_id
                                ELSE null
                            END ) = (CASE
                                WHEN ib2b.invb2b_pm = "Full Payment" THEN 
                                    ib2b.invb2b_id 
                                WHEN ib2b.invb2b_pm = "Installment" THEN 
                                        ib2bd.invdtl_id
                                ELSE null
                            END)
                        LEFT JOIN tbl_sch_prog ib2bsp ON ib2bsp.id = ib2b.schprog_id
                        LEFT JOIN tbl_sch ib2bs ON ib2bs.sch_id = ib2bsp.sch_id
                        LEFT JOIN tbl_partner_prog ib2bpp ON ib2bpp.id = ib2b.partnerprog_id
                        LEFT JOIN tbl_referral ib2brf ON ib2brf.id = ib2b.ref_id
                        LEFT JOIN tbl_corp ib2bc ON ib2bc.corp_id = (CASE 
                                WHEN ib2b.partnerprog_id > 0 THEN ib2bpp.corp_id 
                                WHEN ib2b.ref_id > 0 THEN ib2brf.partner_id 
                                ELSE NULL 
                            END)
                        LEFT JOIN program ib2bp ON ib2bp.prog_id = (CASE 
                                WHEN ib2b.schprog_id > 0 THEN ib2bsp.prog_id
                                    WHEN ib2b.partnerprog_id > 0 THEN ib2bpp.prog_id
                                ELSE NULL
                            END)
                WHERE ib2br.id IS NULL AND
                (CASE
                    WHEN ib2b.schprog_id > 0 THEN ib2bsp.status
                    WHEN ib2b.partnerprog_id > 0 THEN ib2bpp.status
                    WHEN ib2b.ref_id > 0 THEN ib2brf.referral_type
                    ELSE NULL
                END) = (CASE
                    WHEN ib2b.ref_id > 0 THEN "Out"
                    ELSE 1
                END)
            ORDER BY inv_id_year ASC, inv_id_month ASC, inv_id_num ASC
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outstanding_payment_view');
    }
};
