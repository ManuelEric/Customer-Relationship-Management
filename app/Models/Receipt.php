<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'tbl_receipt';
    protected $appends = ['raw_receipt_amount', 'raw_receipt_amount_idr', 'raw_pph23', 'raw_pph23_idr', 'str_pph23', 'str_pph23_idr'];

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
        'receipt_date',
        'pph23',
        'download_idr',
        'download_other',
        'created_at',
        'updated_at'
    ];

    public function getCurrencyUnit()
    {
        $currency = "usd"; # default
        if ($this->inv_id)
            $currency = $this->invoiceProgram->currency;
        elseif ($this->invb2b_id)
            $currency = $this->invoiceB2b->currency;
        elseif ($this->invdtl_id)
            $currency = $this->invoiceInstallment->invdtl_currency;

        switch ($currency) {

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

    protected function rawReceiptAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (int)str_replace($this->getCurrencyUnit(). ' ', '', $this->receipt_amount),
        );
    }

    protected function rawReceiptAmountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (int)filter_var($this->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT),
        );
    }
    
    protected function rawPph23(): Attribute
    {
        $invTotPrice = 0;
        if ($this->inv_id)
            $invTotPrice = $this->invoiceProgram->inv_totalprice;
        elseif ($this->invb2b_id && !$this->invdtl_id)
            $invTotPrice = $this->invoiceB2b->invb2b_totprice;
        elseif ($this->invdtl_id)
            $invTotPrice = $this->invoiceInstallment->invdtl_amount;

        $calcPPH23 = ($this->pph23/100) * ($invTotPrice);
    
        return Attribute::make(
            get: fn ($value) => $calcPPH23,
        );
    }

    protected function rawPph23Idr(): Attribute
    {
        $invTotPrice = 0;
        if ($this->inv_id)
            $invTotPrice = $this->invoiceProgram->inv_totalprice_idr;
        elseif ($this->invb2b_id && !$this->invdtl_id)
            $invTotPrice = $this->invoiceB2b->invb2b_totpriceidr;
        elseif ($this->invdtl_id)
            $invTotPrice = $this->invoiceInstallment->invdtl_amountidr;

        $calcPPH23 = $this->pph23/100 * $invTotPrice;
    
        return Attribute::make(
            get: fn ($value) => $calcPPH23,
        );
    }
    protected function strPph23(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . " " . $this->rawPph23,
        );
    }

    protected function strPph23Idr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " .number_format($this->rawPph23Idr),
        );
    }

    protected function getReceiptAmountAttribute($value)
    {
        return $this->getCurrencyUnit() . " " . $value;
    }

    protected function getReceiptAmountIdrAttribute($value)
    {
        return "Rp. " . number_format($value);
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
        return $this->hasMany(ReceiptAttachment::class, 'receipt_id', 'receipt_id');
    }
}
