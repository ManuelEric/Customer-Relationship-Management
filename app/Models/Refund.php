<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'tbl_refund';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'invb2b_id',
        'inv_id',
        'invdtl_id',
        'total_payment',
        'percentage_payment',
        'refunded_amount',
        'refunded_tax_amount',
        'refunded_tax_percentage',
        'total_refunded',
        'status',
    ];

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function invoiceInstallment()
    {
        return $this->belongsTo(InvDetail::class, 'invdtl_id', 'invdtl_id');
    }

    public function invoiceB2B()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }
}
