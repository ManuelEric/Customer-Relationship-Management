<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $trx_id
 * @property string|null $invoice_id
 * @property string|null $installment_id
 * @property string $invoice_number
 * @property string $trx_currency
 * @property string $trx_amount
 * @property string $item_title
 * @property string $payment_method
 * @property string|null $bank_id
 * @property string|null $bank_name
 * @property string $payment_page_url
 * @property string|null $va_number
 * @property string $merchant_ref_no
 * @property string $plink_ref_no
 * @property string $validity
 * @property string $payment_status settled, rejected, pending
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Transaction available()
 * @method static Builder<static>|Transaction newModelQuery()
 * @method static Builder<static>|Transaction newQuery()
 * @method static Builder<static>|Transaction paid()
 * @method static Builder<static>|Transaction query()
 * @method static Builder<static>|Transaction whereBankId($value)
 * @method static Builder<static>|Transaction whereBankName($value)
 * @method static Builder<static>|Transaction whereCreatedAt($value)
 * @method static Builder<static>|Transaction whereIdentifier(int $installment, $identifier)
 * @method static Builder<static>|Transaction whereInstallmentId($value)
 * @method static Builder<static>|Transaction whereInvoiceId($value)
 * @method static Builder<static>|Transaction whereInvoiceNumber($value)
 * @method static Builder<static>|Transaction whereItemTitle($value)
 * @method static Builder<static>|Transaction whereMerchantRefNo($value)
 * @method static Builder<static>|Transaction wherePaymentMethod($value)
 * @method static Builder<static>|Transaction wherePaymentPageUrl($value)
 * @method static Builder<static>|Transaction wherePaymentStatus($value)
 * @method static Builder<static>|Transaction wherePlinkRefNo($value)
 * @method static Builder<static>|Transaction whereTrxAmount($value)
 * @method static Builder<static>|Transaction whereTrxCurrency($value)
 * @method static Builder<static>|Transaction whereTrxId($value)
 * @method static Builder<static>|Transaction whereUpdatedAt($value)
 * @method static Builder<static>|Transaction whereVaNumber($value)
 * @method static Builder<static>|Transaction whereValidity($value)
 *
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    protected $primaryKey = 'trx_id';

    public $incrementing = false;

    protected $fillable = [
        'trx_id',
        'invoice_id',
        'installment_id',
        'invoice_number',
        'trx_currency',
        'trx_amount',
        'item_title',
        'payment_method',
        'bank_id',
        'bank_name',
        'payment_page_url',
        'va_number',
        'merchant_ref_no',
        'plink_ref_no',
        'validity',
        'payment_status',
    ];

    public function scopeWhereIdentifier(Builder $query, int $installment, $identifier): void
    {
        $query->when($installment == 0, function ($query) use ($identifier) {
            $query->where('invoice_id', $identifier);
        }, function ($query) use ($identifier) {
            $query->where('installment_id', $identifier);
        });
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->where('validity', '>=', Carbon::now())
            ->where('payment_status', 'PNDNG');
    }

    public function scopePaid(Builder $query): void
    {
        $query->where('payment_status', 'SETLD');
    }

    public function scopeWhereBankName(Builder $query, $bank_name = null): void
    {
        $query->when($bank_name, function ($query) use ($bank_name) {
            $query->where('bank_name', $bank_name);
        });
    }
}
