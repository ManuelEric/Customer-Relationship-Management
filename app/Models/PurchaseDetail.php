<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_purchase_dtl';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'item',
        'amount',
        'price_per_unit',
        'notes',
        'total',
    ];

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_id', 'purchase_id');
    }
}
