<?php

namespace App\Interfaces;

interface InvoiceB2bRepositoryInterface
{
    public function getAllInvoiceNeededSchDataTables();
    public function getAllInvoiceSchDataTables(string $status);
    public function getAllDueDateInvoiceSchoolProgram(int $days);
    public function getAllInvoiceNeededCorpDataTables();
    public function getAllInvoiceCorpDataTables(string $status);
    public function getAllDueDateInvoicePartnerProgram(int $days);
    public function getAllInvoiceNeededReferralDataTables();
    public function getAllInvoiceReferralDataTables(string $status);
    public function getAllDueDateInvoiceReferralProgram(int $days);
    public function getInvoiceB2bByInvId($invb2b_id);
    public function getInvoiceB2bBySchProg($schprog_id);
    public function getInvoiceB2bByPartnerProg($partnerprog_id);
    public function getInvoiceB2bById($invb2b_num);
    public function getAllInvoiceSchool();
    public function deleteInvoiceB2b($invb2b_num);
    public function createInvoiceB2b(array $invoices);
    public function insertInvoiceB2b(array $invoices);
    public function updateInvoiceB2b($invb2b_num, array $invoices);
    public function getReportInvoiceB2b($start_date, $end_date);
    public function getReportUnpaidInvoiceB2b($start_date, $end_date);
    public function getTotalPartnershipProgram($monthYear);
    public function getTotalInvoiceNeeded($monthYear);
    public function getTotalInvoice($monthYear);
    public function getTotalRefundRequest($monthYear);
    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null);
    public function getRevenueByYear($year);
    public function getAllInvoiceSchoolFromCRM();
}
