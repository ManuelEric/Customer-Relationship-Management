<?php

namespace App\Models\pivot;

use App\Models\AssetReturned;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $asset_id
 * @property string $user_id
 * @property string $used_date
 * @property int $amount_used
 * @property string $condition
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AssetReturned> $returned_detail
 * @property-read int|null $returned_detail_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereAmountUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereUsedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetUsed whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AssetUsed extends Pivot
{
    protected $table = 'tbl_asset_used';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'asset_id',
        'user_id',
        'used_date',
        'amount_used',
        'condition',
        'notes',
        'created_at',
        'updated_at',
    ];

    // public function usedDate(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => date('F d, Y', strtotime($value)),
    //     );
    // }

    // relation
    public function returned_detail()
    {
        return $this->hasMany(AssetReturned::class, 'asset_used_id', 'id');
    }
}
