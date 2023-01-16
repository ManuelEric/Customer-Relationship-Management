<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'tbl_receipt';

    protected $fillabled = [
        'receipt_id',
        'receipt_cat',
        'inv_id',
        'invdtl_id',
        'invb2b_id',
        'receipt_method',
        'receipt_cheque',
        'receipt_amount',
        'receipt_words',
        'receipt_amount_idr',
        'receipt_words_idr',
        'receipt_notes',
        'receipt_status',
        'rfd_total_payment',
        'rfd_percentage_payment',
        'refund_amount',
        'refund_tax_percentage',
        'refund_tax_amount',
        'total_refunded',
        'created_at',
        'updated_at',
    ];

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function invoiceB2b()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }

    public function invoiceInstallment()
    {
        return $this->belongsTo(InvDetail::class, 'invdtl_id', 'invdtl_id');
    }
}
