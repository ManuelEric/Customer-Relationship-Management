<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_inv';
    protected $appends = ['total_refund', 'total_refund_str'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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
        'updated_at'
    ];

    # Modify methods Model
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
            get: fn ($value) => "Rp. " . number_format($this->curs_rate)
        );
    }

    protected function invoicePriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_price_idr)
        );
    }

    protected function invoiceEarlybirdIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_earlybird_idr)
        );
    }

    protected function invoiceDiscountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_discount_idr)
        );
    }

    protected function invoiceTotalpriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->inv_totalprice_idr)
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
            get: fn ($value) => "Rp. " . number_format($this->totalRefund)
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

    public function invoiceAttachment()
    {
        return $this->hasMany(InvoiceAttachment::class, 'inv_id', 'inv_id');
    }

    /**
     * 
     * Get the bundling for the invoice
     * 
     */
    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id', 'uuid');
    }
}
