<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Invb2b extends Model
{
    use HasFactory;

    protected $table = 'tbl_invb2b';
    protected $primaryKey = 'invb2b_num';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'invb2b_id',
        'schprog_id',
        'partnerprog_id',
        'invb2b_price',
        'invb2b_priceidr',
        'invb2b_participants',
        'invb2b_disc',
        'invb2b_discidr',
        'invb2b_totprice',
        'invb2b_totpriceidr',
        'invb2b_words',
        'invb2b_wordsidr',
        'invb2b_date',
        'invb2b_duedate',
        'invb2b_pm',
        'invb2b_notes',
        'invb2b_tnc',
        'invb2b_status',
        'curs_rate',
        'currency',
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
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->invb2b_price
        );
    }

    protected function invoiceDiscount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->invb2b_disc
        );
    }

    protected function invoiceTotalprice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->invb2b_totprice
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
            get: fn ($value) => "Rp. " . number_format($this->invb2b_priceidr, '2', ',', '.')
        );
    }

    protected function invoiceDiscountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->invb2b_discidr, '2', ',', '.')
        );
    }

    protected function invoiceTotalpriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->invb2b_totpriceidr, '2', ',', '.')
        );
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

    public function inv_detail()
    {
        return $this->hasMany(InvDetail::class, 'invb2b_id', 'invb2b_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'invb2b_id', 'invb2b_id');
    }

    public function sch_prog()
    {
        return $this->hasOne(SchoolProgram::class, 'id', 'schprog_id');
    }
}
