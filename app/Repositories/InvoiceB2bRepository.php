<?php

namespace App\Repositories;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Models\Invb2b;
use App\Models\PartnerProg;
use App\Models\Referral;
use App\Models\SchoolProgram;
use App\Models\v1\InvoiceSchool as V1InvoiceSchool;
use Carbon\Carbon;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InvoiceB2bRepository implements InvoiceB2bRepositoryInterface
{

    // School Program
    public function getAllInvoiceNeededSchDataTables()
    {
        return datatables::eloquent(
            SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')
                ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
                // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.schprog_id', '=', 'tbl_sch_prog.id')
                ->select(
                    'tbl_sch.sch_id',
                    'tbl_sch_prog.id',
                    'tbl_sch.sch_name as school_name',
                    // 'tbl_prog.prog_program as program_name',
                    'program.program_name',
                    'tbl_sch_prog.success_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )->where('tbl_sch_prog.status', 1)->whereNull('tbl_invb2b.schprog_id')
        )->filterColumn(
            'pic_name',
            function ($query, $keyword) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )
            ->make(true);
    }

    public function getAllInvoiceSchDataTables($status)
    {
        switch ($status) {

            case 'list':
                $query = Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                    ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->select(
                        'tbl_invb2b.invb2b_num',
                        'tbl_sch.sch_name as school_name',
                        // 'tbl_prog.prog_program as program_name',
                        'program.program_name',
                        'tbl_invb2b.schprog_id',
                        'tbl_invb2b.invb2b_id',
                        'tbl_invb2b.invb2b_status',
                        'tbl_invb2b.invb2b_pm',
                        'tbl_invb2b.created_at',
                        'tbl_invb2b.invb2b_duedate',
                        'tbl_invb2b.currency',
                        'tbl_invb2b.invb2b_totpriceidr',
                        'tbl_invb2b.invb2b_totprice',
                        'tbl_sch_prog.status'
                    )
                    ->where('tbl_sch_prog.status', '!=', 0);
                break;

            case 'reminder':
                $query = Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                    ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->select(
                        'tbl_invb2b.invb2b_num',
                        'tbl_sch.sch_name as school_name',
                        // 'tbl_prog.prog_program as program_name',
                        'program.program_name',
                        'tbl_invb2b.schprog_id',
                        'tbl_invb2b.invb2b_id',
                        'tbl_invb2b.invb2b_status',
                        DB::raw('
                            (CASE
                                WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_pm
                                WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_installment
                            END) as invb2b_pm
                        '),
                        // 'tbl_invb2b.invb2b_pm',
                        DB::raw('
                            (CASE
                                WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.created_at
                                WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.created_at
                            END) as created_at
                        '),
                        // 'tbl_invb2b.created_at',
                        DB::raw('
                            (CASE
                                WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_duedate
                                WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_duedate
                            END) as invb2b_duedate
                        '),
                        // 'tbl_invb2b.invb2b_duedate',
                        'tbl_invb2b.currency',
                        DB::raw('
                            (CASE
                                WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_totpriceidr
                                WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_amountidr
                            END) as invb2b_totpriceidr
                        '),
                        // 'tbl_invb2b.invb2b_totpriceidr',
                        'tbl_invb2b.invb2b_totprice',
                        DB::raw('
                            (CASE
                                WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                                WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                            END) as date_difference
                        '),
                        // DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now()) as date_difference')
                    )
                    ->where('tbl_sch_prog.status', 1)
                    ->whereDoesntHave('receipt')
                    ->where(DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                    END)
                '), '<=', 7);
                break;
        }
        return DataTables::eloquent($query)->make(true);
    }

    public function getAllDueDateInvoiceSchoolProgram($days)
    {
        return Invb2b::
            leftJoin('tbl_inv_attachment', 'tbl_invb2b.invb2b_id', '=', 'tbl_inv_attachment.invb2b_id')
            ->leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('users as u', 'u.id', '=', 'tbl_sch_prog.empl_id')
            ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_invb2b.invb2b_num',
                'tbl_sch.sch_name as school_name',
                // 'tbl_prog.prog_program as program_name',
                'program.program_name',
                'tbl_invb2b.schprog_id',
                'tbl_invb2b.invb2b_id',
                'tbl_invb2b.invb2b_status',
                'tbl_invb2b.invb2b_pm',
                'tbl_invb2b.created_at',
                'tbl_inv_attachment.sign_status as sign_status',
                DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_duedate
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_duedate
                    END) as invb2b_duedate
                '),
                // 'tbl_invb2b.invb2b_duedate',
                'tbl_invb2b.currency',
                DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_totpriceidr
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_amountidr
                    END) as invb2b_totpriceidr
                '),
                // 'tbl_invb2b.invb2b_totpriceidr',
                'tbl_invb2b.invb2b_totprice',
                'u.email as pic_mail',
                DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                    END) as date_difference
                '),
                // DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now()) as date_difference')
            )
            ->where('tbl_sch_prog.status', 1)
            ->where('tbl_invb2b.reminded', '=', 0)
            ->whereDoesntHave('receipt')
            ->where(DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                    END)
                '), '=', $days)
            ->orderBy('date_difference', 'asc')->get();
    }

    // Partner Program
    public function getAllInvoiceNeededCorpDataTables()
    {   
        return datatables::eloquent(
            PartnerProg::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                ->leftJoin('program', 'program.prog_id', '=', 'tbl_partner_prog.prog_id')
                // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.partnerprog_id', '=', 'tbl_partner_prog.id')
                ->select(
                    'tbl_partner_prog.id',
                    'tbl_corp.corp_id',
                    'tbl_corp.corp_name as partner_name',
                    // 'tbl_prog.prog_program as program_name',
                    'program.program_name',
                    'tbl_partner_prog.success_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )->where('tbl_partner_prog.status', 1)->whereNull('tbl_invb2b.partnerprog_id')
        )->filterColumn(
            'pic_name',
            function (
                $query,
                $keyword
            ) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllInvoiceCorpDataTables($status)
    {
        switch ($status) {

            case 'list':
                $query = Invb2b::leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
                    ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_partner_prog.prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->select(
                        'tbl_invb2b.invb2b_num',
                        'tbl_corp.corp_name',
                        // 'tbl_prog.prog_program as program_name',
                        'program.program_name',
                        'tbl_invb2b.partnerprog_id',
                        'tbl_invb2b.invb2b_id',
                        'tbl_invb2b.invb2b_status',
                        'tbl_invb2b.invb2b_pm',
                        'tbl_invb2b.created_at',
                        'tbl_invb2b.invb2b_duedate',
                        'tbl_invb2b.currency',
                        'tbl_invb2b.invb2b_totpriceidr',
                        'tbl_invb2b.invb2b_totprice',
                        'tbl_partner_prog.status'
                    )
                    // ->where('tbl_partner_prog.status', 1)
                    ->where('tbl_partner_prog.status', '!=', 0);
                // ->where('tbl_invb2b.invb2b_status', 1);
                break;

            case 'reminder':
                $query = Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
                    ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_partner_prog.prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->select(
                        'tbl_invb2b.invb2b_num',
                        'tbl_corp.corp_name',
                        // 'tbl_prog.prog_program as program_name',
                        'program.program_name',
                        'tbl_invb2b.partnerprog_id',
                        'tbl_invb2b.invb2b_id',
                        'tbl_invb2b.invb2b_status',
                        DB::raw('
                                (CASE
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_pm
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_installment
                                END) as invb2b_pm
                            '),
                        // 'tbl_invb2b.invb2b_pm',
                        DB::raw('
                                (CASE
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.created_at
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.created_at
                                END) as created_at
                            '),
                        // 'tbl_invb2b.created_at',
                        DB::raw('
                                (CASE
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_duedate
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_duedate
                                END) as invb2b_duedate
                            '),
                        // 'tbl_invb2b.invb2b_duedate',
                        'tbl_invb2b.currency',
                        DB::raw('
                                (CASE
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_totpriceidr
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_amountidr
                                END) as invb2b_totpriceidr
                            '),
                        // 'tbl_invb2b.invb2b_totpriceidr',
                        'tbl_invb2b.invb2b_totprice',
                        DB::raw('
                                (CASE
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                                END) as date_difference
                            '),
                        // DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now()) as date_difference')
                    )
                    ->where('tbl_partner_prog.status', 1)
                    ->whereDoesntHave('receipt')
                    ->where(DB::raw('
                            (CASE
                                WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                                WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                            END)'), '<=', 7);
                break;
        }
        $response = DataTables::eloquent($query)->make(true);

        return $response;
    }

    public function getAllDueDateInvoicePartnerProgram($days)
    {
        return Invb2b::
            leftJoin('tbl_inv_attachment', 'tbl_invb2b.invb2b_id', '=', 'tbl_inv_attachment.invb2b_id')->
            leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')->
            leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')->
            leftJoin('users as u', 'u.id', '=', 'tbl_partner_prog.empl_id')->
            leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')->
            leftJoin('program', 'program.prog_id', '=', 'tbl_partner_prog.prog_id')->
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            select(
                'tbl_invb2b.invb2b_num',
                'tbl_corp.corp_name',
                // 'tbl_prog.prog_program as program_name',
                'program.program_name',
                'tbl_invb2b.partnerprog_id',
                'tbl_invb2b.invb2b_id',
                'tbl_invb2b.invb2b_status',
                'tbl_invb2b.invb2b_pm',
                'tbl_invb2b.created_at',
                'tbl_inv_attachment.sign_status as sign_status',
                DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_duedate
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_duedate
                    END) as invb2b_duedate
                '),
                // 'tbl_invb2b.invb2b_duedate',
                'tbl_invb2b.currency',
                DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN tbl_invb2b.invb2b_totpriceidr
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN tbl_invdtl.invdtl_amountidr
                    END) as invb2b_totpriceidr
                '),
                // 'tbl_invb2b.invb2b_totpriceidr',
                'tbl_invb2b.invb2b_totprice',
                'u.email as pic_mail',
                DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                    END) as date_difference
                '),
                // DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now()) as date_difference')
            )
            ->where('tbl_partner_prog.status', 1)
            ->where('tbl_invb2b.reminded', '=', 0)
            ->whereDoesntHave('receipt')
            ->where(DB::raw('
                    (CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                    END)
                '), '=', $days)
            // ->where(DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now())'), '=', $days)
            ->orderBy('date_difference', 'asc')->get();
    }

    // Referral
    public function getAllInvoiceNeededReferralDataTables()
    {
        return DataTables::eloquent(
            Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
                // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_referral.empl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.ref_id', '=', 'tbl_referral.id')
                ->select(
                    'tbl_referral.id',
                    'tbl_corp.corp_id',
                    'tbl_corp.corp_name as partner_name',
                    DB::raw('(CASE tbl_referral.referral_type
                                WHEN "Out" THEN tbl_referral.additional_prog_name
                                WHEN "In" 
                                    THEN 
                                        program.program_name
                            END) AS program_name'),
                    'tbl_referral.number_of_student',
                    'tbl_referral.ref_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )->where('tbl_referral.referral_type', 'Out')->whereNull('tbl_invb2b.ref_id')
        )->filterColumn(
            'pic_name',
            function (
                $query,
                $keyword
            ) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllInvoiceReferralDataTables($status)
    {
        switch ($status) {

            case 'list':
                $query = Invb2b::leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
                    ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->select(
                        'tbl_invb2b.invb2b_num',
                        'tbl_corp.corp_name as partner_name',
                        DB::raw('(CASE tbl_referral.referral_type
                                    WHEN "Out" THEN tbl_referral.additional_prog_name
                                    WHEN "In" 
                                        THEN 
                                            program.program_name
                                END) AS program_name'),
                        'tbl_invb2b.ref_id',
                        'tbl_invb2b.invb2b_id',
                        'tbl_invb2b.invb2b_status',
                        'tbl_invb2b.invb2b_pm',
                        'tbl_invb2b.created_at',
                        'tbl_invb2b.invb2b_duedate',
                        'tbl_invb2b.currency',
                        'tbl_invb2b.invb2b_totpriceidr',
                        'tbl_invb2b.invb2b_totprice',
                    )
                    ->where('tbl_referral.referral_type', 'Out');
                break;

            case 'reminder':
                $query = Invb2b::leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
                    ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->select(
                        'tbl_invb2b.invb2b_num',
                        'tbl_corp.corp_name as partner_name',
                        DB::raw('(CASE tbl_referral.referral_type
                                    WHEN "Out" THEN tbl_referral.additional_prog_name
                                    WHEN "In" 
                                        THEN 
                                            program.program_name
                                END) AS program_name'),
                        'tbl_invb2b.ref_id',
                        'tbl_invb2b.invb2b_id',
                        'tbl_invb2b.invb2b_status',
                        'tbl_invb2b.invb2b_pm',
                        'tbl_invb2b.created_at',
                        'tbl_invb2b.invb2b_duedate',
                        'tbl_invb2b.currency',
                        'tbl_invb2b.invb2b_totpriceidr',
                        'tbl_invb2b.invb2b_totprice',
                        DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now()) as date_difference')
                    )
                    ->whereDoesntHave('receipt')
                    ->where(DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now())'), '<=', 7)
                    ->where('tbl_referral.referral_type', 'Out');
                break;
        }

        return datatables::eloquent($query)->make(true);
    }

    public function getAllDueDateInvoiceReferralProgram($days)
    {
        return Invb2b::
            leftJoin('tbl_inv_attachment', 'tbl_invb2b.invb2b_id', '=', 'tbl_inv_attachment.invb2b_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->leftJoin('users as u', 'u.id', '=', 'tbl_referral.empl_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_invb2b.invb2b_num',
                'tbl_corp.corp_name as partner_name',
                DB::raw('(CASE tbl_referral.referral_type
                        WHEN "Out" THEN tbl_referral.additional_prog_name
                        WHEN "In" 
                            THEN 
                                program.program_name
                    END) AS program_name'),
                'tbl_invb2b.ref_id',
                'tbl_invb2b.invb2b_id',
                'tbl_invb2b.invb2b_status',
                'tbl_invb2b.invb2b_pm',
                'tbl_invb2b.created_at',
                'tbl_invb2b.invb2b_duedate',
                'tbl_invb2b.currency',
                'tbl_invb2b.invb2b_totpriceidr',
                'tbl_invb2b.invb2b_totprice',
                'u.email as pic_mail',
                'tbl_inv_attachment.sign_status as sign_status',
                DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now()) as date_difference')
            )
            ->whereDoesntHave('receipt')
            ->where(DB::raw('DATEDIFF(tbl_invb2b.invb2b_duedate, now())'), '=', $days)
            ->where('tbl_referral.referral_type', 'Out')
            ->where('tbl_invb2b.reminded', '=', 0)
            ->orderBy('date_difference', 'asc')->get();
    }

    public function getInvoiceB2bBySchProg($schprog_id)
    {
        return Invb2b::where('schprog_id', $schprog_id)->first();
    }

    public function getInvoiceB2bByPartnerProg($partnerprog_id)
    {
        return Invb2b::where('partnerprog_id', $partnerprog_id)->first();
    }

    public function getInvoiceB2bByInvId($invb2b_id)
    {
        return Invb2b::where('invb2b_id', $invb2b_id)->get();
    }

    public function getInvoiceB2bById($invb2b_num)
    {
        return Invb2b::find($invb2b_num);
    }

    public function getAllInvoiceSchool()
    {
        return Invb2b::whereNotNull('schprog_id')->get();
    }

    public function deleteInvoiceB2b($invb2b_num)
    {
        return Invb2b::destroy($invb2b_num);
    }

    public function createInvoiceB2b(array $invoices)
    {
        return Invb2b::create($invoices);
    }

    public function insertInvoiceB2b(array $invoices)
    {
        return Invb2b::insert($invoices);
    }

    public function updateInvoiceB2b($invb2b_num, array $newInvoices)
    {
        return Invb2b::find($invb2b_num)->update($newInvoices);
    }

    public function getReportInvoiceB2b($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $queryInv = Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id');
            // ->where(
            //     DB::raw('(CASE
            //                     WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
            //                     WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
            //                     WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type 
            //                 END)'),
            //     DB::raw('(CASE
            //                     WHEN tbl_invb2b.ref_id > 0 THEN "Out" 
            //                     ELSE 1
            //                 END)')
            // );

        if (isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.created_at', '>=', $start_date)
                ->whereDate('tbl_invb2b.created_at', '<=', $end_date);
        } else if (isset($start_date) && !isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.created_at', '>=', $start_date);
        } else if (!isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.created_at', '<=', $end_date);
        } else {
            $queryInv->whereBetween('tbl_invb2b.created_at', [$firstDay, $lastDay]);
        }

        return $queryInv->orderBy('tbl_invb2b.invb2b_id', 'ASC')->withCount('inv_detail')->get();
    }


    public function getReportUnpaidInvoiceB2b($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $whereBy = DB::raw('(CASE 
                            WHEN tbl_receipt.id is not null THEN
                                tbl_receipt.created_at
                            ELSE 
                                (CASE 
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                        tbl_invb2b.invb2b_duedate 
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                        tbl_invdtl.invdtl_duedate
                                END)
                        END)');

        $invoiceB2b = Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_receipt.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_invb2b.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')


            ->select(
                'tbl_invb2b.invb2b_id',
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 1), '/', -1) as 'invb2b_id_num'"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 4), '/', -1) as 'invb2b_id_month'"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 5), '/', -1) as 'invb2b_id_year'"),
                'tbl_invb2b.schprog_id',
                'tbl_invb2b.partnerprog_id',
                'tbl_invb2b.ref_id',
                'tbl_invb2b.invb2b_duedate as invoice_duedate',
                'tbl_invb2b.currency',
                'tbl_invdtl.invdtl_duedate as installment_duedate',
                DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_totpriceidr 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total_price_inv_idr'),
                DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_totprice 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_amount
                            ELSE null
                        END) as total_price_inv_other'),
                'tbl_receipt.receipt_id',
                'tbl_receipt.receipt_amount_idr',
                'tbl_receipt.created_at as paid_date',
                'tbl_invdtl.invdtl_installment',
                'tbl_invdtl.invdtl_id',
            )
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out" 
                                ELSE 1
                            END)')
            )->where('tbl_receipt.receipt_id', '=', NULL);

        if (isset($start_date) && isset($end_date)) {
            return $invoiceB2b->whereBetween($whereBy, [$start_date, $end_date])
                ->orderBy('invb2b_id_num', 'asc')
                ->orderBy('invb2b_id_month', 'asc')
                ->orderBy('invb2b_id_year', 'asc')
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return $invoiceB2b->whereDate($whereBy, '>=', $start_date)
                ->orderBy('invb2b_id_num', 'asc')
                ->orderBy('invb2b_id_month', 'asc')
                ->orderBy('invb2b_id_year', 'asc')
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return $invoiceB2b->whereDate($whereBy, '<=', $end_date)
                ->orderBy('invb2b_id_num', 'asc')
                ->orderBy('invb2b_id_month', 'asc')
                ->orderBy('invb2b_id_year', 'asc')
                ->get();
        } else {
            return $invoiceB2b->whereBetween($whereBy, [$firstDay, $lastDay])
                ->orderBy('invb2b_id_num', 'asc')
                ->orderBy('invb2b_id_month', 'asc')
                ->orderBy('invb2b_id_year', 'asc')
                ->get();
        }
    }

    public function getTotalPartnershipProgram($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $schProg = Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->select(DB::raw("'sch_prog' as 'type'"), 'tbl_invb2b.invb2b_totpriceidr')
            ->where('tbl_sch_prog.status', '1')
            ->whereYear('tbl_sch_prog.created_at', '=', $year)
            ->whereMonth('tbl_sch_prog.created_at', '=', $month);

        $partnerProg = Invb2b::leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->select(DB::raw("'partner_prog' as 'type'"), 'tbl_invb2b.invb2b_totpriceidr')
            ->where('tbl_partner_prog.status', '1')
            ->whereYear('tbl_partner_prog.created_at', '=', $year)
            ->whereMonth('tbl_partner_prog.created_at', '=', $month)
            ->union($schProg)
            ->get();

        return $partnerProg;
    }

    public function getTotalInvoiceNeeded($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $schProg  = SchoolProgram::doesntHave('invoiceB2b')
            ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
            ->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')
            ->select(
                'tbl_sch.sch_name as client_name',
                'program.program_name',
                DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name'),
                'tbl_sch_prog.success_date',
                'tbl_sch_prog.id as client_prog_id',
                DB::raw("'sch_prog' as type"),
            )
            ->where('tbl_sch_prog.status', 1)
            ->when($monthYear != null, function ($query) use ($month, $year){
                $query->where(function ($query2) use($month, $year){
                    $query2->where(function ($q) use($month, $year){
                        $q->whereYear('tbl_sch_prog.success_date', '=', $year)->whereMonth('tbl_sch_prog.success_date', '=', $month);
                    })->orWhere(function ($q) use ($month, $year){
                        $q->whereYear('tbl_sch_prog.created_at', '=', $year)->whereMonth('tbl_sch_prog.created_at', '=', $month);
                    });
                });
            })
            ->get();

        $partnerProg  = PartnerProg::doesntHave('invoiceB2b')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_partner_prog.prog_id')
            ->leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')
            ->select(
                'tbl_corp.corp_name as client_name',
                'program.program_name',
                DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name'),
                'tbl_partner_prog.success_date',
                'tbl_partner_prog.id as client_prog_id',
                DB::raw("'partner_prog' as type"),
            )
            ->where('tbl_partner_prog.status', 1)
            ->when($monthYear != null, function ($query) use ($month, $year){
                $query->where(function ($query2) use($month, $year){
                    $query2->where(function ($q) use($month, $year){
                        $q->whereYear('tbl_partner_prog.success_date', '=', $year)->whereMonth('tbl_partner_prog.success_date', '=', $month);
                    })->orWhere(function ($q) use ($month, $year){
                        $q->whereYear('tbl_partner_prog.created_at', '=', $year)->whereMonth('tbl_partner_prog.created_at', '=', $month);
                    });
                });
            })
            ->get();

        $referral  = Referral::doesntHave('invoice')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
            // ->leftJoin('program', 'program.prog_id', '=', 'tbl_referral.prog_id')
            ->leftJoin('users', 'users.id', '=', 'tbl_referral.empl_id')
            ->select(
                'tbl_corp.corp_name as client_name',
                'tbl_referral.additional_prog_name as program_name',
                DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name'),
                'tbl_referral.ref_date as success_date',
                'tbl_referral.id as client_prog_id',
                DB::raw("'referral' as type"),

            )->where('tbl_referral.referral_type', 'Out')
            ->when($monthYear != null, function ($query) use ($month, $year){
                $query->where(function ($query2) use($month, $year){
                    $query2->where(function ($q) use($month, $year){
                        $q->whereYear('tbl_referral.ref_date', '=', $year)->whereMonth('tbl_referral.ref_date', '=', $month);
                    })->orWhere(function ($q) use ($month, $year){
                        $q->whereYear('tbl_referral.created_at', '=', $year)->whereMonth('tbl_referral.created_at', '=', $month);
                    });
                });
            })
            ->get();

        $collection = collect($schProg);
        return $collection->merge($partnerProg)->merge($referral);
    }


    public function getTotalInvoice($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $whereBy = DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_duedate 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                        END)');

        return Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            // ->select(DB::raw('COUNT(invb2b_num) as count_invoice'), DB::raw('CAST(sum(invb2b_totpriceidr) as integer) as total'))
            ->select(
                'tbl_invb2b.invb2b_num',
                'tbl_invdtl.invdtl_id',
                'tbl_invb2b.invb2b_totpriceidr',
                'tbl_invb2b.invb2b_pm',
                'tbl_invdtl.invdtl_amountidr'
            )
            ->whereYear($whereBy, '=', $year)
            ->whereMonth($whereBy, '=', $month)
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            ELSE NULL
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                            ELSE 1
                            END)')
            )
            ->get();
    }

    public function getTotalRefundRequest($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $schProg = SchoolProgram::leftJoin('tbl_invb2b', 'tbl_invb2b.schprog_id', '=', 'tbl_sch_prog.id')
            ->select(DB::raw("count('tbl_sch_prog.id') as count_refund_request"))
            ->where('tbl_sch_prog.status', 3)
            ->where('tbl_invb2b.invb2b_status', 1)
            ->whereYear('tbl_sch_prog.refund_date', '=', $year)
            ->whereMonth('tbl_sch_prog.refund_date', '=', $month)
            ->get();

        $partnerProg = PartnerProg::leftJoin('tbl_invb2b', 'tbl_invb2b.partnerprog_id', '=', 'tbl_partner_prog.id')
            ->select(DB::raw("count('tbl_partner_prog.id') as count_refund_request"))
            ->where('tbl_partner_prog.status', 3)
            ->where('tbl_invb2b.invb2b_status', 1)
            ->whereYear('tbl_partner_prog.refund_date', '=', $year)
            ->whereMonth('tbl_partner_prog.refund_date', '=', $month)
            ->get();

        $collection = collect($schProg);
        return $collection->merge($partnerProg);
    }

    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null)
    {
        $whereBy = DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_duedate 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                        END)');

        if (isset($monthYear)) {
            $year = date('Y', strtotime($monthYear));
            $month = date('m', strtotime($monthYear));
        }

        $queryInv = Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_receipt.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_invb2b.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', DB::raw('(CASE WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.corp_id WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.partner_id ELSE NULL END)'))
            ->leftJoin(
                'program',
                'program.prog_id',
                '=',
                DB::raw('(CASE 
                            WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.prog_id
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.prog_id
                            ELSE NULL
                            END)')
            );
        // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id');

        switch ($type) {
            case 'paid':
                $queryInv->select(
                    'tbl_invb2b.invb2b_id as invoice_id',
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 1), '/', -1) as 'invb2b_id_num'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 4), '/', -1) as 'invb2b_id_month'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 5), '/', -1) as 'invb2b_id_year'"),
                    'tbl_invdtl.invdtl_id',
                    DB::raw('(CASE 
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch.sch_name
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_corp.corp_name
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_corp.corp_name
                            ELSE NULL
                            END) as full_name'),
                    DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.additional_prog_name 
                                ELSE
                                    program.program_name
                            END) AS program_name'),
                    'tbl_invb2b.invb2b_totpriceidr as total_price_inv',
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'B2B' as type"),
                    DB::raw('tbl_receipt.receipt_amount_idr as total'),
                )->whereNotNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear('tbl_receipt.created_at', '=', $year)
                        ->whereMonth('tbl_receipt.created_at', '=', $month);
                } else {
                    $queryInv->whereBetween('tbl_receipt.created_at', [$start_date, $end_date]);
                }
                break;

            case 'unpaid':
                $queryInv->select(
                    'tbl_invb2b.invb2b_id as invoice_id',
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 1), '/', -1) as 'invb2b_id_num'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 4), '/', -1) as 'invb2b_id_month'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_invb2b.invb2b_id, '/', 5), '/', -1) as 'invb2b_id_year'"),
                    'tbl_invdtl.invdtl_id',
                    DB::raw('(CASE 
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch.sch_name
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_corp.corp_name
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_corp.corp_name
                            ELSE NULL
                            END) as full_name'),
                    DB::raw('(CASE 
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_invb2b.schprog_id
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_invb2b.partnerprog_id
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_invb2b.ref_id
                            ELSE NULL
                            END) as client_prog_id'),
                    DB::raw('(CASE 
                                WHEN tbl_invb2b.schprog_id > 0 THEN "sch_prog"
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN "partner_prog"
                                WHEN tbl_invb2b.ref_id > 0 THEN "referral"
                            ELSE NULL
                            END) as typeprog'),
                    DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.additional_prog_name 
                                ELSE
                                   program.program_name
                            END) AS program_name'),
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'B2B' as type"),
                    DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_totpriceidr 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total'),
                    DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_duedate
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                            ELSE null
                        END) as invoice_duedate'),
                    // DB::raw("'start_data '" . $start_date . "as start_date"),

                )->whereNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear($whereBy, '=', $year)
                        ->whereMonth($whereBy, '=', $month);
                } else {
                    $queryInv->whereBetween($whereBy, [$start_date, $end_date]);
                }
                break;
        }

        $queryInv->where(
            DB::raw('(CASE
                        WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                        WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                        WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                    ELSE NULL
                    END)'),
            DB::raw('(CASE
                        WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                    ELSE 1
                    END)')
        )->orderBy('invb2b_id_year', 'asc')
            ->orderBy('invb2b_id_month', 'asc')
            ->orderBy('invb2b_id_num', 'asc')
        ->groupBy(DB::raw('(CASE WHEN tbl_invdtl.invdtl_id is null THEN tbl_invb2b.invb2b_id ELSE tbl_invdtl.invdtl_id END)'));


        return $queryInv->get();
    }

    public function getRevenueByYear($year)
    {
        return Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_receipt.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                            tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_invb2b.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                            tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->select(DB::raw('SUM(tbl_receipt.receipt_amount_idr) as total'), DB::raw('MONTH(tbl_receipt.created_at) as month'))
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            ELSE NULL
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                            ELSE 1
                            END)')
            )
            ->whereNotNull('tbl_receipt.id')
            ->whereYear('tbl_receipt.created_at', '=', $year)
            ->groupBy(DB::raw('MONTH(tbl_receipt.created_at)'))
            ->get();
    }

    # CRM
    public function getAllInvoiceSchoolFromCRM()
    {
        return V1InvoiceSchool::all();
    }
}
