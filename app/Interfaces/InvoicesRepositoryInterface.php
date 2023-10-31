<?php

namespace App\Interfaces;

interface InvoicesRepositoryInterface
{
    public function getOustandingPaymentDataTables($monthYear);
}
