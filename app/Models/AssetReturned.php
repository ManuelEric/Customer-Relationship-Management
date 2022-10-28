<?php

namespace App\Models;

use App\Models\pivot\AssetUsed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetReturned extends Model
{
    use HasFactory;

    protected $table = 'tbl_asset_returned';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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

    # relation
    public function used_detail()
    {
        return $this->belongsTo(AssetUsed::class, 'asset_used_id', 'id');
    }
}
