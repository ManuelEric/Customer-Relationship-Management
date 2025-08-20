<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $purchase_id
 * @property string $item
 * @property int $amount
 * @property int $price_per_unit
 * @property string|null $notes
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PurchaseRequest $purchase_request
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail wherePricePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_purchase_dtl';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
