<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $receipt_id
 * @property string $currency
 * @property string $sign_status
 * @property string|null $recipient
 * @property string|null $approve_date
 * @property string $send_to_client
 * @property string|null $attachment
 * @property string $request_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Receipt $receipt
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereApproveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereRecipient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereRequestStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereSendToClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereSignStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReceiptAttachment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ReceiptAttachment extends Model
{
    use HasFactory;

    protected $table = 'tbl_receipt_attachment';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
