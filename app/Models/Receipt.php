<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'tbl_receipt';

    protected $fillable = [
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
        'download_idr',
        'download_other',
        'created_at',
        'updated_at'
    ];

    public function getCurrencyUnit()
    {
        switch ($this->currency) {

            case "usd":
            default:
                $unit = '$';
                break;

            case "sgd":
                $unit = 'S$';
                break;

            case "gbp":
                $unit = '£';
                break;
        }

        return $unit;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    protected function getReceiptAmountAttribute($value)
    {
        return $this->getCurrencyUnit() . " " . $value;
    }

    protected function getReceiptAmountIdrAttribute($value)
    {
        return "Rp. " . number_format($value, '2', ',', '.');
    }


    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->receipt_amount != NULL ? $this->receipt_amount : $this->receipt_amount_idr,
        );
    }

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

    public function receiptAttachment()
    {
        return $this->hasMany(receiptAttachment::class, 'receipt_id', 'receipt_id');
    }
}
