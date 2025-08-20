<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $inv_id
 * @property string|null $invb2b_id
 * @property string $currency
 * @property string $sign_status
 * @property string|null $recipient
 * @property string|null $approve_date
 * @property string $send_to_client
 * @property string $attachment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invb2b|null $invoiceB2b
 * @property-read \App\Models\InvoiceProgram|null $invoiceProgram
 *
 * @method static Builder<static>|InvoiceAttachment newModelQuery()
 * @method static Builder<static>|InvoiceAttachment newQuery()
 * @method static Builder<static>|InvoiceAttachment query()
 * @method static Builder<static>|InvoiceAttachment selectAttachment($invoice_type, $identifier, $currency)
 * @method static Builder<static>|InvoiceAttachment whereApproveDate($value)
 * @method static Builder<static>|InvoiceAttachment whereAttachment($value)
 * @method static Builder<static>|InvoiceAttachment whereCreatedAt($value)
 * @method static Builder<static>|InvoiceAttachment whereCurrency($value)
 * @method static Builder<static>|InvoiceAttachment whereId($value)
 * @method static Builder<static>|InvoiceAttachment whereInvId($value)
 * @method static Builder<static>|InvoiceAttachment whereInvb2bId($value)
 * @method static Builder<static>|InvoiceAttachment whereRecipient($value)
 * @method static Builder<static>|InvoiceAttachment whereSendToClient($value)
 * @method static Builder<static>|InvoiceAttachment whereSignStatus($value)
 * @method static Builder<static>|InvoiceAttachment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InvoiceAttachment extends Model
{
    use HasFactory;

    protected $table = 'tbl_inv_attachment';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'inv_id',
        'invb2b_id',
        'currency',
        'sign_status',
        'recipient',
        'approve_date',
        'send_to_client',
        'attachment',
    ];

    public function scopeSelectAttachment(Builder $query, $invoice_type, $identifier, $currency)
    {
        $query->
            when($invoice_type == 'B2B', function ($query) use ($identifier, $currency) { // for invoice type: B2B
                $query->where('invb2b_id', $identifier)->where('currency', $currency);
            }, function ($query) use ($identifier, $currency) { // for invoice type: Program as default
                $query->where('inv_id', $identifier)->where('currency', $currency);
            });
    }

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function invoiceB2b()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }
}
