<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_inv';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'inv_id',
        'clientprog_id',
        'ref_id',
        'inv_category',
        'inv_price',
        'inv_earlybird',
        'inv_discount',
        'inv_totalprice',
        'inv_words',
        'inv_price_idr',
        'inv_earlybird_idr',
        'inv_discount_idr',
        'inv_totalprice_idr',
        'inv_words_idr',
        'session',
        'duration',
        'inv_paymentmethod',
        'inv_duedate',
        'inv_notes',
        'inv_tnc',
        'inv_status',
        'curs_rate',
        'currency',
        'attachment',
        'send_to_client',
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

    protected function invoicePrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->inv_price
        );
    }

    protected function invoiceEarlybird(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->inv_earlybird
        );
    }

    protected function invoiceDiscount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->inv_discount
        );
    }

    protected function invoiceTotalprice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->inv_totalprice
        );
    }

    protected function rate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->curs_rate, '2', ',', '.')
        );
    }

    protected function invoicePriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_price_idr, '2', ',', '.')
        );
    }

    protected function invoiceEarlybirdIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_earlybird_idr, '2', ',', '.')
        );
    }

    protected function invoiceDiscountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_discount_idr, '2', ',', '.')
        );
    }

    protected function invoiceTotalpriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_totalprice_idr, '2', ',', '.')
        );
    }

    public function clientprog()
    {
        return $this->belongsTo(ClientProgram::class, 'clientprog_id', 'clientprog_id');
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class, 'ref_id', 'id');
    }

    public function invoiceDetail()
    {
        return $this->hasMany(InvDetail::class, 'inv_id', 'inv_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'inv_id', 'inv_id');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'inv_id', 'inv_id');
    }

}
