<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_inv';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'inv_id',
        'clientprog_id',
        'ref_id',
        'inv_category',
        'inv_price',
        'inv_earlybird',
        'inv_discount',
        'inv_totalprice',
        'inv_words',
        'inv_price_idr',
        'inv_price',
        'inv_earlybird_idr',
        'inv_discount_idr',
        'inv_totalprice_idr',
        'inv_words_idr',
        'session',
        'duration',
        'inv_paymentmethod',
        'inv_duedate',
        'inv_notes',
        'inv_tnc',
        'curs_rate',
        'currency',
        'inv_status'
    ];

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
}
