<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $inv_id
 * @property int|null $clientprog_id
 * @property string|null $bundling_id
 * @property int|null $ref_id
 * @property string|null $inv_category
 * @property int|null $inv_price
 * @property int $inv_earlybird
 * @property int $inv_discount
 * @property int $inv_totalprice
 * @property string|null $inv_words
 * @property int|null $inv_price_idr
 * @property int|null $inv_earlybird_idr
 * @property int|null $inv_discount_idr
 * @property int $inv_totalprice_idr
 * @property string|null $inv_words_idr
 * @property int $session
 * @property int $duration
 * @property string $inv_paymentmethod
 * @property string|null $invoice_date
 * @property string|null $inv_duedate
 * @property string|null $inv_notes
 * @property string|null $inv_tnc
 * @property int $inv_status 1: success, 2: refund
 * @property int $curs_rate
 * @property string|null $currency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $send_to_client
 * @property int $reminded jumlah reminder terkirim
 * @property-read \App\Models\Bundling|null $bundling
 * @property-read \App\Models\ClientProgram|null $clientprog
 * @property-read \App\Models\Receipt|null $firstReceipt
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceAttachment> $invoiceAttachment
 * @property-read int|null $invoice_attachment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvDetail> $invoiceDetail
 * @property-read int|null $invoice_detail_count
 * @property-read mixed $invoice_discount
 * @property-read mixed $invoice_discount_idr
 * @property-read mixed $invoice_earlybird
 * @property-read mixed $invoice_earlybird_idr
 * @property-read mixed $invoice_price
 * @property-read mixed $invoice_price_idr
 * @property-read mixed $invoice_totalprice
 * @property-read mixed $invoice_totalprice_idr
 * @property-read mixed $rate
 * @property-read \App\Models\Receipt|null $receipt
 * @property-read \App\Models\Referral|null $referral
 * @property-read \App\Models\Refund|null $refund
 * @property-read mixed $total_refund
 * @property-read mixed $total_refund_str
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereBundlingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereCursRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvDiscountIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvDuedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvEarlybird($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvEarlybirdIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvPaymentmethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvPriceIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvTnc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvTotalprice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvTotalpriceIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvWords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvWordsIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereInvoiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereReminded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereSendToClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceProgram whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InvoiceProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_inv';

    protected $appends = ['total_refund', 'total_refund_str'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'inv_id',
        'bundling_id',
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
        'invoice_date',
        'inv_duedate',
        'inv_notes',
        'inv_tnc',
        'inv_status',
        'curs_rate',
        'currency',
        'send_to_client',
        'reminded',
        'created_at',
        'updated_at',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_invoice_b2c', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_invoice_b2c', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_invoice_b2c', 'channel_datatable'));

        return $model;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function getCurrencyUnit()
    {
        switch ($this->currency) {

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

    protected function invoicePrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->inv_price
        );
    }

    protected function invoiceEarlybird(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->inv_earlybird
        );
    }

    protected function invoiceDiscount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->inv_discount
        );
    }

    protected function invoiceTotalprice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->inv_totalprice
        );
    }

    protected function rate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->curs_rate)
        );
    }

    protected function invoicePriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->inv_price_idr)
        );
    }

    protected function invoiceEarlybirdIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->inv_earlybird_idr)
        );
    }

    protected function invoiceDiscountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->inv_discount_idr)
        );
    }

    protected function invoiceTotalpriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->inv_totalprice_idr)
        );
    }

    protected function totalRefund(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->refund) ? $this->refund->total_refunded : 0
        );
    }

    protected function totalRefundStr(): Attribute
    {

        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->totalRefund)
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

    public function firstReceipt()
    {
        return $this->hasOne(Receipt::class, 'inv_id', 'inv_id')->oldestOfMany();
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'inv_id', 'inv_id');
    }

    public function invoiceAttachment()
    {
        return $this->hasMany(InvoiceAttachment::class, 'inv_id', 'inv_id');
    }

    /**
     * Get the bundling for the invoice
     */
    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id', 'uuid');
    }
}
