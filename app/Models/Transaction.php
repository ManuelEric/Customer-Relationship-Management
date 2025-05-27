<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    
    protected $primaryKey  = 'trx_id';
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
        'payment_status'
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
