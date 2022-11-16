<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'purchase_statusrequest',
        'purchase_requestdate',
        'purchase_notes',
        'purchase_attachment',
    ];

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d F Y', strtotime($value)),
        );
    }

    public function detail()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'purchase_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'purchase_department', 'id');
    }
}
