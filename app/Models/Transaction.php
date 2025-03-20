<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    
    protected $primary = 'trx_id';

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
}
