<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class VendorType extends Model
{
    use HasFactory;

    protected $table = 'tbl_vendor_type';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];
}
