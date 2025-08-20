<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $invdtl_id
 * @property string|null $invb2b_id
 * @property string|null $inv_id
 * @property string|null $invdtl_installment
 * @property string|null $invdtl_duedate
 * @property float|null $invdtl_percentage
 * @property int|null $invdtl_amount
 * @property int|null $invdtl_amountidr
 * @property int $invdtl_status
 * @property int|null $invdtl_cursrate
 * @property string|null $invdtl_currency
 * @property int $reminded has been reminded = 1 else 0
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invb2b|null $inv_b2b
 * @property-read \App\Models\InvoiceProgram|null $invoiceProgram
 * @property-read mixed $invoicedtl_amount
 * @property-read mixed $invoicedtl_amountidr
 * @property-read \App\Models\Receipt|null $receipt
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvb2bId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlAmountidr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlCursrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlDuedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlInstallment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereInvdtlStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereReminded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InvDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_invdtl';

    protected $primaryKey = 'invdtl_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
        'updated_at',
    ];

    public function getCurrencyUnit()
    {
        switch ($this->invdtl_currency) {

            case 'usd':
            default:
                $unit = '$';
                break;

            case 'sgd':
                $unit = 'S$';
                break;

            case 'gbp':
                $unit = '£';
                break;

            case 'aud':
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
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->invdtl_amount
        );
    }

    protected function invoicedtlAmountidr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->invdtl_amountidr)
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
