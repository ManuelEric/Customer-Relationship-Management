<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property string $vendor_id
 * @property string|null $vendor_name
 * @property string|null $vendor_address
 * @property string|null $vendor_phone
 * @property string|null $vendor_type
 * @property string|null $vendor_material
 * @property string|null $vendor_size
 * @property int $vendor_unitprice
 * @property string|null $vendor_processingtime
 * @property string|null $vendor_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorMaterial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorProcessingtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorUnitprice($value)
 *
 * @mixin \Eloquent
 */
class Vendor extends Model
{
    use HasFactory;

    protected $table = 'tbl_vendor';

    protected $primaryKey = 'vendor_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor_id',
        'vendor_name',
        'vendor_address',
        'vendor_phone',
        'vendor_type',
        'vendor_material',
        'vendor_size',
        'vendor_unitprice',
        'vendor_processingtime',
        'vendor_notes',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_vendor', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_vendor', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_vendor', 'channel_datatable'));

        return $model;
    }

    public static function whereVendorId($id)
    {
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('vendor_id', $id)->first();
    }
}
