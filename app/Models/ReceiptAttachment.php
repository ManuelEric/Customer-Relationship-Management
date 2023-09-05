<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;


class ReceiptAttachment extends Model
{
    use HasFactory;

    protected $table = 'tbl_receipt_attachment';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'receipt_id',
        'currency',
        'sign_status',
        'recipient',
        'approve_date',
        'send_to_client',
        'attachment',
        'request_status',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id', 'receipt_id');
    }
}
