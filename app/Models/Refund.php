<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'total_payment',
        'total_paid',
        'refund_amount',
        'percentage_refund',
        'tax_amount',
        'tax_percentage',
        'total_refunded',
        'status',
    ];

    protected function totalRefundedStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->total_refunded, '2', ',', '.'),
        );
    }

    protected function totalPaidStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->total_paid, '2', ',', '.'),
        );
    }

    protected function refundAmountStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->refund_amount, '2', ',', '.'),
        );
    }

    protected function taxAmountStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->tax_amount, '2', ',', '.'),
        );
    }

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function invoiceB2B()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }
}
