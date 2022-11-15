<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $table = 'tbl_purchase_request';
    protected $primaryKey = 'purchase_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'requested_by',
        'purchase_department',
        'purchase_usedfor',
        'purchase_statusrequest',
        'purchase_notes',
        'purchase_attachment',
    ];

    public function detail()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'purchase_id');
    }
}
