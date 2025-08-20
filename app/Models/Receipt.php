<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $receipt_id
 * @property string|null $receipt_cat
 * @property string|null $inv_id
 * @property int|null $invdtl_id
 * @property string|null $invb2b_id
 * @property string|null $receipt_method
 * @property string|null $receipt_cheque
 * @property int|null $receipt_amount
 * @property string|null $receipt_words
 * @property int|null $receipt_amount_idr
 * @property string|null $receipt_words_idr
 * @property string|null $receipt_notes
 * @property string|null $receipt_date
 * @property int|null $pph23
 * @property int $receipt_status 1: success, 2: refund
 * @property int $download_other 0: Not Yet, 1: Downloaded
 * @property int $download_idr 0: Not Yet, 1: Downloaded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invb2b|null $invoiceB2b
 * @property-read \App\Models\InvDetail|null $invoiceInstallment
 * @property-read \App\Models\InvoiceProgram|null $invoiceProgram
 * @property-read mixed $raw_pph23
 * @property-read mixed $raw_pph23_idr
 * @property-read mixed $raw_receipt_amount
 * @property-read mixed $raw_receipt_amount_idr
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReceiptAttachment> $receiptAttachment
 * @property-read int|null $receipt_attachment_count
 * @property-read mixed $str_pph23
 * @property-read mixed $str_pph23_idr
 * @property-read mixed $total_amount
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereDownloadIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereDownloadOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereInvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereInvb2bId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereInvdtlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt wherePph23($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptAmountIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptCat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptCheque($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptWords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereReceiptWordsIdr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Receipt whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Receipt extends Model
{
    use HasFactory;

    protected $table = 'tbl_receipt';

    protected $appends = ['raw_receipt_amount', 'raw_receipt_amount_idr', 'raw_pph23', 'raw_pph23_idr', 'str_pph23', 'str_pph23_idr'];

    protected $fillable = [
        'receipt_id',
        'receipt_cat',
        'inv_id',
        'invdtl_id',
        'invb2b_id',
        'receipt_method',
        'receipt_cheque',
        'receipt_amount',
        'receipt_words',
        'receipt_amount_idr',
        'receipt_words_idr',
        'receipt_notes',
        'receipt_status',
        'rfd_total_payment',
        'rfd_percentage_payment',
        'refund_amount',
        'refund_tax_percentage',
        'refund_tax_amount',
        'total_refunded',
        'receipt_date',
        'pph23',
        'download_idr',
        'download_other',
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
        event(new MessageSent('rt_receipt', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_receipt', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_receipt', 'channel_datatable'));

        return $model;
    }

    public function getCurrencyUnit()
    {
        $currency = 'usd'; // default
        if ($this->inv_id) {
            $currency = $this->invoiceProgram->currency;
        } elseif ($this->invb2b_id) {
            $currency = $this->invoiceB2b->currency;
        } elseif ($this->invdtl_id) {
            $currency = $this->invoiceInstallment->invdtl_currency;
        }

        switch ($currency) {

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
        }

        return $unit;
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

    protected function rawReceiptAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (int) str_replace($this->getCurrencyUnit().' ', '', $this->receipt_amount),
        );
    }

    protected function rawReceiptAmountIdr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (int) filter_var($this->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT),
        );
    }

    protected function rawPph23(): Attribute
    {
        $invTotPrice = 0;
        if ($this->inv_id) {
            $invTotPrice = $this->invoiceProgram->inv_totalprice;
        } elseif ($this->invb2b_id && ! $this->invdtl_id) {
            $invTotPrice = $this->invoiceB2b->invb2b_totprice;
        } elseif ($this->invdtl_id) {
            $invTotPrice = $this->invoiceInstallment->invdtl_amount;
        }

        $calcPPH23 = ($this->pph23 / 100) * ($invTotPrice);

        return Attribute::make(
            get: fn ($value) => $calcPPH23,
        );
    }

    protected function rawPph23Idr(): Attribute
    {
        $invTotPrice = 0;
        if ($this->inv_id) {
            $invTotPrice = $this->invoiceProgram->inv_totalprice_idr;
        } elseif ($this->invb2b_id && ! $this->invdtl_id) {
            $invTotPrice = $this->invoiceB2b->invb2b_totpriceidr;
        } elseif ($this->invdtl_id) {
            $invTotPrice = $this->invoiceInstallment->invdtl_amountidr;
        }

        $calcPPH23 = $this->pph23 / 100 * $invTotPrice;

        return Attribute::make(
            get: fn ($value) => $calcPPH23,
        );
    }

    protected function strPph23(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getCurrencyUnit().' '.$this->rawPph23,
        );
    }

    protected function strPph23Idr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp. '.number_format($this->rawPph23Idr),
        );
    }

    protected function getReceiptAmountAttribute($value)
    {
        return $this->getCurrencyUnit().' '.$value;
    }

    protected function getReceiptAmountIdrAttribute($value)
    {
        return 'Rp. '.number_format($value);
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->receipt_amount != null ? $this->receipt_amount : $this->receipt_amount_idr,
        );
    }

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function invoiceB2b()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }

    public function invoiceInstallment()
    {
        return $this->belongsTo(InvDetail::class, 'invdtl_id', 'invdtl_id');
    }

    public function receiptAttachment()
    {
        return $this->hasMany(ReceiptAttachment::class, 'receipt_id', 'receipt_id');
    }
}
