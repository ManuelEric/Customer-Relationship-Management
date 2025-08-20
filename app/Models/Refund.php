<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $invb2b_id
 * @property string|null $inv_id
 * @property int $total_payment
 * @property int $total_paid
 * @property float $refund_amount
 * @property float $percentage_refund
 * @property float $tax_amount
 * @property float $tax_percentage
 * @property float $total_refunded
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invb2b|null $invoiceB2B
 * @property-read \App\Models\InvoiceProgram|null $invoiceProgram
 * @property-read mixed $refund_amount_str
 * @property-read mixed $tax_amount_str
 * @property-read mixed $total_paid_str
 * @property-read mixed $total_refunded_str
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereInvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereInvb2bId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund wherePercentageRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereTaxPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereTotalPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereTotalPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereTotalRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Refund whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Refund extends Model
{
    use HasFactory;

    protected $table = 'tbl_refund';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_refund', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_refund', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_refund', 'channel_datatable'));

        return $model;
    }

    protected function totalRefundedStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->total_refunded),
        );
    }

    protected function totalPaidStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->total_paid),
        );
    }

    protected function refundAmountStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->refund_amount),
        );
    }

    protected function taxAmountStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->tax_amount),
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
