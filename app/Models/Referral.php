<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $partner_id
 * @property string|null $prog_id
 * @property string $empl_id Internal PIC
 * @property string $referral_type
 * @property string|null $additional_prog_name
 * @property int $number_of_student
 * @property string $currency
 * @property int|null $curs_rate
 * @property int $revenue
 * @property int|null $revenue_other
 * @property string $ref_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invb2b> $invoice
 * @property-read int|null $invoice_count
 * @property-read \App\Models\Corporate $partner
 * @property-read \App\Models\Program|null $program
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereAdditionalProgName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereCursRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereEmplId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereNumberOfStudent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereRefDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferralType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereRevenueOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Referral extends Model
{
    use HasFactory;

    protected $table = 'tbl_referral';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'partner_id',
        'prog_id',
        'empl_id',
        'referral_type',
        'additional_prog_name',
        'currency',
        'curs_rate',
        'number_of_student',
        'revenue',
        'revenue_other',
        'ref_date',
        'notes',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_referral', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_referral', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_referral', 'channel_datatable'));

        return $model;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function invoice()
    {
        return $this->hasMany(Invb2b::class, 'ref_id', 'id');
    }

    public function partner()
    {
        return $this->belongsTo(Corporate::class, 'partner_id', 'corp_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'empl_id', 'id');
    }
}
