<?php

namespace App\Repositories;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\ViewClientProgram;
use DataTables;

class InvoiceProgramRepository implements InvoiceProgramRepositoryInterface 
{
    public function getAllInvoiceProgramDataTables($status)
    {
        switch($status) {

            case "needed":
                $query = ViewClientProgram::when($status == "needed", function ($q) {
                    # select all client program
                    # where status already success which mean they(client) already paid the program
                    $q->doesntHave('invoice')->where('status', 1);
                });
                break;

            case "list":
                $query = InvoiceProgram::query();
                break;

        }
        

        return DataTables::eloquent($query)->make(true);
    }

    public function createInvoice(array $invoiceDetails)
    {
        return InvoiceProgram::create($invoiceDetails);
    }
}