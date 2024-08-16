<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'tbl_refund';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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

    # Modify methods Model
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
            get: fn ($value) => "Rp. " . number_format($this->total_refunded),
        );
    }

    protected function totalPaidStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->total_paid),
        );
    }

    protected function refundAmountStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->refund_amount),
        );
    }

    protected function taxAmountStr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Rp. " . number_format($this->tax_amount),
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
