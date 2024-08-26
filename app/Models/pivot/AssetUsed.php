<?php

namespace App\Models\pivot;

use App\Models\AssetReturned;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AssetUsed extends Pivot
{
    protected $table = 'tbl_asset_used';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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

    # relation
    public function returned_detail()
    {
        return $this->hasMany(AssetReturned::class, 'asset_used_id', 'id');
    }
}
