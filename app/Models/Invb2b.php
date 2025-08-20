<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $invb2b_num
 * @property string $invb2b_id
 * @property int|null $schprog_id
 * @property int|null $partnerprog_id
 * @property int|null $ref_id
 * @property int|null $invb2b_price
 * @property int|null $invb2b_priceidr
 * @property int|null $invb2b_participants
 * @property int|null $invb2b_disc
 * @property int|null $invb2b_discidr
 * @property int|null $invb2b_totprice
 * @property int|null $invb2b_totpriceidr
 * @property string|null $invb2b_words
 * @property string|null $invb2b_wordsidr
 * @property string $invb2b_date
 * @property string|null $invb2b_duedate
 * @property string $invb2b_pm
 * @property string|null $invb2b_notes
 * @property string|null $invb2b_tnc
 * @property int $invb2b_status 1: Success, 2: Refund
 * @property int|null $curs_rate
 * @property string|null $currency
 * @property int $is_full_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $reminded jumlah reminder terkirim
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvDetail> $inv_detail
 * @property-read int|null $inv_detail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceAttachment> $invoiceAttachment
 * @property-read int|null $invoice_attachment_count
 * @property-read mixed $invoice_discount
 * @property-read mixed $invoice_discount_idr
 * @property-read mixed $invoice_price
 * @property-read mixed $invoice_price_idr
 * @property-read mixed $invoice_sub_totalprice
 * @property-read mixed $invoice_sub_totalprice_idr
 * @property-read mixed $invoice_totalprice
 * @property-read mixed $invoice_totalprice_idr
 * @property-read \App\Models\PartnerProg|null $partner_prog
 * @property-read mixed $rate
 * @property-read \App\Models\Receipt|null $receipt
 * @property-read \App\Models\Referral|null $referral
 * @property-read \App\Models\Refund|null $refund
 * @property-read \App\Models\SchoolProgram|null $sch_prog
 * @property-read mixed $total_refund
 * @property-read mixed $total_refund_str
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereCursRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bDisc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bDiscidr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bDuedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bPm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bPriceidr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bTnc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bTotprice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bTotpriceidr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bWords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereInvb2bWordsidr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereIsFullAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b wherePartnerprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereReminded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereSchprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invb2b whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Invb2b extends Model
{
    use HasFactory;

    protected $table = 'tbl_invb2b';

    protected $primaryKey = 'invb2b_num';

    protected $appends = ['total_refund', 'total_refund_str'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invb2b_id',
        'schprog_id',
        'partnerprog_id',
        'ref_id',
        'invb2b_price',
        'invb2b_priceidr',
        'invb2b_participants',
        'invb2b_disc',
        'invb2b_discidr',
        'invb2b_totprice',
        'invb2b_totpriceidr',
        'invb2b_words',
        'invb2b_wordsidr',
        'invb2b_date',
        'invb2b_duedate',
        'invb2b_pm',
        'invb2b_notes',
        'invb2b_tnc',
        'invb2b_status',
        'curs_rate',
        'currency',
        'attachment',
        'send_to_client',
        'sign_status',
        'approve_date',
        'is_full_amount',
        'reminded',
        'created_at',
        'updated_at',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_invoice_b2b', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_invoice_b2b', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_invoice_b2b', 'channel_datatable'));

        return $model;
    }

    public function getCurrencyUnit()
    {
        switch ($this->currency) {

            case 'usd':
            default:
                $unit = '$';
                break;

            case 'sgd':
                $unit = 'S$';
                break;

            case 'gbp':
                $unit = '£';
                break;

            case 'aud':
                $unit = 'A$';
                break;
        }

        return $unit;
    }

    protected function invoicePrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->invb2b_price
        );
    }

    protected function invoiceDiscount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->invb2b_disc
        );
    }

    protected function invoiceTotalprice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->invb2b_totprice
        );
    }

    protected function invoiceSubTotalprice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->invb2b_price * ($this->invb2b_participants == 0 ? 1 : $this->invb2b_participants)
        );
    }

    protected function rate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->curs_rate)
        );
    }

    protected function invoicePriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->invb2b_priceidr)
        );
    }

    protected function invoiceDiscountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->invb2b_discidr)
        );
    }

    protected function invoiceTotalpriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->invb2b_totpriceidr)
        );
    }

    protected function invoiceSubTotalpriceIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->invb2b_priceidr * ($this->invb2b_participants == 0 ? 1 : $this->invb2b_participants))
        );
    }

    protected function totalRefund(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->refund) ? $this->refund->total_refunded : 0
        );
    }

    protected function totalRefundStr(): Attribute
    {

        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->totalRefund)
        );
    }

    public function invb2bDuedate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($value)),
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function inv_detail()
    {
        return $this->hasMany(InvDetail::class, 'invb2b_id', 'invb2b_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'invb2b_id', 'invb2b_id');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'invb2b_id', 'invb2b_id');
    }

    public function sch_prog()
    {
        return $this->hasOne(SchoolProgram::class, 'id', 'schprog_id');
    }

    public function referral()
    {
        return $this->hasOne(Referral::class, 'id', 'ref_id');
    }

    public function partner_prog()
    {
        return $this->hasOne(PartnerProg::class, 'id', 'partnerprog_id');
    }

    public function invoiceAttachment()
    {
        return $this->hasMany(InvoiceAttachment::class, 'invb2b_id', 'invb2b_id');
    }
}
