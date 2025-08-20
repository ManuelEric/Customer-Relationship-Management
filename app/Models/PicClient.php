<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $client_id
 * @property string $user_id
 * @property int $status 0: non active, 1: active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicClient whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PicClient extends Model
{
    use HasFactory;

    protected $table = 'tbl_pic_client';

    public $timestamps = true;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'user_id',
        'status',
        'created_at',
        'updated_at',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    // relation
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
