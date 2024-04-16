<?php

namespace App\Models;

use App\Models\Invb2b;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class InvDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_invdtl';
    protected $primaryKey = 'invdtl_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'invdtl_id',
        'invb2b_id',
        'inv_id',
        'invdtl_installment',
        'invdtl_duedate',
        'invdtl_percentage',
        'invdtl_amount',
        'invdtl_amountidr',
        'invdtl_status',
        'invdtl_cursrate',
        'invdtl_currency',
        'created_at',
        'updated_at'
    ];

    public function getCurrencyUnit()
    {
        switch ($this->invdtl_currency) {

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

            case "aud":
                $unit = 'A$';
                break;
        }

        return $unit;
    }

    public function invdtlDuedate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($value)),
        );
    }

    protected function invoicedtlAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit() . ' ' . $this->invdtl_amount
        );
    }

    protected function invoicedtlAmountidr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->invdtl_amountidr)
        );
    }

    public function inv_b2b()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'invdtl_id', 'invdtl_id');
    }
}
