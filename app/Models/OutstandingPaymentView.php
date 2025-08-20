<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $invoice_id
 * @property string $inv_id_num
 * @property string $inv_id_month
 * @property string $inv_id_year
 * @property string|null $full_name
 * @property int|null $client_prog_id
 * @property string|null $program_name
 * @property string|null $installment_name
 * @property string $type
 * @property int|null $total
 * @property string|null $invoice_duedate
 * @property int|null $clientprog_id
 * @property string|null $client_id
 * @property string|null $child_phone
 * @property string|null $parent_phone
 * @property string|null $parent_name
 * @property string|null $parent_id
 * @property string|null $typeprog
 * @property string $payment_method
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereChildPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereClientProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereInstallmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereInvIdMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereInvIdNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereInvIdYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereInvoiceDuedate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereParentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereParentPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereProgramName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutstandingPaymentView whereTypeprog($value)
 *
 * @mixin \Eloquent
 */
class OutstandingPaymentView extends Model
{
    use HasFactory;

    protected $table = 'outstanding_payment_view';
}
