<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $header_name
 * @property string $value
 * @property string $expires_at
 *
 * @method static \Database\Factories\TokenLibFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TokenLib newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TokenLib newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TokenLib query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TokenLib whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TokenLib whereHeaderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TokenLib whereValue($value)
 *
 * @mixin \Eloquent
 */
class TokenLib extends Model
{
    use HasFactory;

    protected $table = 'token_lib';

    public $timestamps = false;

    protected $fillable = [
        'header_name',
        'value',
        'expires_at',
    ];
}
