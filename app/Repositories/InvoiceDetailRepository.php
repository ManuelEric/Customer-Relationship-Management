<?php

namespace App\Repositories;

use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Models\InvDetail;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvoiceDetailRepository implements InvoiceDetailRepositoryInterface
{

    public function getInvoiceDetailById($identifier)
    {
        return InvDetail::find($identifier);
    }

    public function getInvoiceDetailIdByInvB2b($invb2b_id, $invdtl_installment)
    {
        return InvDetail::where('invb2b_id', $invb2b_id)->where('invdtl_installment', $invdtl_installment)->pluck('invdtl_id');
    }

    public function getInvoiceDetailByInvB2bId($invb2b_id)
    {
        return InvDetail::where('invb2b_id', $invb2b_id)->get();
    }

    public function getInvoiceDetailByInvB2bIdnName($invb2b_id, $name)
    {
        return InvDetail::where('invb2b_id', $invb2b_id)->where('invdtl_installment', $name)->first();
    }

    public function getInvoiceDetailByInvIdandName($invoiceId, $name)
    {
        return InvDetail::where('inv_id', $invoiceId)->where('invdtl_installment', $name)->first();
    }

    public function getInvoiceDetailByInvId($invoiceId)
    {
        return InvDetail::where('inv_id', $invoiceId)->get();
    }

    public function deleteInvoiceDetailById($invdtl_id)
    {
        return InvDetail::destroy($invdtl_id);
    }

    public function deleteInvoiceDetailByinvb2b_Id($invb2b_id)
    {
        return InvDetail::where('invb2b_id', $invb2b_id)->delete();
    }

    public function createOneInvoiceDetail(array $installment)
    {
        return InvDetail::create($installment);
    }

    public function createInvoiceDetail(array $installments)
    {
        return InvDetail::insert($installments);
    }

    public function updateInvoiceDetailByInvId($invoiceId, array $installmentDetails)
    {
        if (InvDetail::where('inv_id', $invoiceId)->exists()) {

            InvDetail::where('inv_id', $invoiceId)->delete();
        }

        return InvDetail::insert($installmentDetails);
    }

    public function deleteInvoiceDetailByInvId($invoiceId)
    {
        return InvDetail::where('inv_id', $invoiceId)->delete();
        
    }

    public function updateInvoiceDetailByInvB2bId($invoiceId, array $installments)
    {
        if (InvDetail::where('invb2b_id', $invoiceId)->exists()) {

            InvDetail::where('invb2b_id', $invoiceId)->delete();
        }
        // InvDetail::where('invdtl_id', $invdtl_id)->update($installments);

    }

    public function getReportUnpaidInvoiceDetail($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        if (isset($start_date) && isset($end_date)) {
            return InvDetail::whereDate('invdtl_duedate', '>=', $start_date)
                ->whereDate('invdtl_duedate', '<=', $end_date)
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return InvDetail::whereDate('invdtl_duedate', '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return InvDetail::whereDate('invdtl_duedate', '<=', $end_date)
                ->get();
        } else {
            return InvDetail::whereBetween('invdtl_duedate', [$firstDay, $lastDay])
                ->get();
        }
    }
}
