<?php

namespace App\Interfaces;

interface ReceiptRepositoryInterface
{
    public function getAllReceiptSchDataTables();
    public function getAllReceiptCorpDataTables();
    public function getAllReceiptReferralDataTables();
    public function getAllReceiptByStatusDataTables($status = null);
    public function getReceiptByInvoiceIdentifier($invoiceType, $identifier);
    public function getReceiptByReceiptId($receiptId);
    public function getReceiptById($receiptId);
    public function getAllReceiptSchool();
    public function createReceipt(array $receiptDetails);
    public function insertReceipt(array $receiptDetails);
    public function updateReceipt($receiptId, array $newDetails);
    public function updateReceiptByInvoiceIdentifier($invoiceType, $identifier, array $newDetails);
    public function deleteReceipt($receiptId);
    public function getReportReceipt($start_date, $end_date);
    public function getTotalReceipt($monthYear);
    public function getDatatables($model);

    # signature
    public function getReceiptsNeedToBeSigned(bool $asDatatables);

    # CRM
    public function getAllReceiptFromCRM();
    public function getReceiptDifferences();
    public function getReceiptRefFromCRM();
}
