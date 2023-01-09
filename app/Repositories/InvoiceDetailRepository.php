<?php

namespace App\Repositories;

use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Models\InvDetail;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvoiceDetailRepository implements InvoiceDetailRepositoryInterface
{

    public function getInvoiceDetailIdByInvB2b($invb2b_id, $invdtl_installment)
    {
        return InvDetail::where('invb2b_id', $invb2b_id)->where('invdtl_installment', $invdtl_installment)->pluck('invdtl_id');
    }

    public function getInvoiceDetailByInvB2bId($invb2b_id)
    {
        return InvDetail::where('invb2b_id', $invb2b_id)->get();
    }

    public function deleteInvoiceDetailById($invdtl_id)
    {
        return InvDetail::destroy($invdtl_id);
    }

    public function createInvoiceDetail(array $installments)
    {
        return InvDetail::insert($installments);
    }

    public function updateInvoiceDetailByInvB2bId($invdtl_id, array $installments)
    {
   
        InvDetail::where('invdtl_id', $invdtl_id)->update($installments);

       
    }
}
