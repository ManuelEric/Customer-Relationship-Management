<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Models\pivot\AssetUsed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property string $asset_id
 * @property string|null $asset_name
 * @property string|null $asset_merktype
 * @property string|null $asset_dateachieved
 * @property int|null $asset_amount
 * @property int $asset_running_stock
 * @property string|null $asset_unit
 * @property string|null $asset_condition
 * @property string|null $asset_notes
 * @property string|null $asset_status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read AssetUsed|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $userUsedAsset
 * @property-read int|null $user_used_asset_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetDateachieved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetMerktype($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetRunningStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Asset extends Model
{
    use HasFactory;

    protected $table = 'tbl_asset';

    protected $primaryKey = 'asset_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'asset_id',
        'asset_name',
        'asset_merktype',
        'asset_dateachieved',
        'asset_amount',
        'asset_running_stock',
        'asset_unit',
        'asset_condition',
        'asset_notes',
        'asset_status',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_asset', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_asset', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model
        // Send to pusher
        event(new MessageSent('rt_asset', 'channel_datatable'));

        return $model;
    }

    public function assetNotes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strip_tags($value),
        );
    }

    public static function whereAssetId($id)
    {
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('asset_id', $id)->first();
    }

    public function userUsedAsset()
    {
        return $this->belongsToMany(User::class, 'tbl_asset_used', 'asset_id', 'user_id')->using(AssetUsed::class)->withPivot(
            [
                'id',
                'used_date',
                'amount_used',
                'condition',
            ]
        );
    }
}
