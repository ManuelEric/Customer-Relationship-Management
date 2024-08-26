<?php

namespace App\Interfaces;

interface InvoicesRepositoryInterface
{
    public function getOustandingPaymentDataTables($monthYear);
    public function getOustandingPaymentPaginate($monthYear, $search = null);
}
