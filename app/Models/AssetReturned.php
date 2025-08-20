<?php

namespace App\Models;

use App\Models\pivot\AssetUsed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $asset_used_id
 * @property string $returned_date
 * @property int $amount_returned
 * @property string $condition
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AssetUsed $used_detail
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereAmountReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereAssetUsedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereReturnedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetReturned whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AssetReturned extends Model
{
    use HasFactory;

    protected $table = 'tbl_asset_returned';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'asset_used_id',
        'returned_date',
        'amount_returned',
        'condition',
        'notes',
        'created_at',
        'updated_at',
    ];

    // relation
    public function used_detail()
    {
        return $this->belongsTo(AssetUsed::class, 'asset_used_id', 'id');
    }
}
