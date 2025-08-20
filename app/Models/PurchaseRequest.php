<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $purchase_id
 * @property string $requested_by
 * @property int $purchase_department
 * @property string $purchase_statusrequest
 * @property string $purchase_requestdate
 * @property string|null $purchase_notes
 * @property string|null $purchase_attachment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $detail
 * @property-read int|null $detail_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest wherePurchaseAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest wherePurchaseDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest wherePurchaseNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest wherePurchaseRequestdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest wherePurchaseStatusrequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PurchaseRequest extends Model
{
    use HasFactory;

    protected $table = 'tbl_purchase_request';

    protected $primaryKey = 'purchase_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_purchase_request', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_purchase_request', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_purchase_request', 'channel_datatable'));

        return $model;
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('F dS Y', strtotime($value)),
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
