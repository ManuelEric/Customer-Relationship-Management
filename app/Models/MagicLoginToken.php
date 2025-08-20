<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagicLoginToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagicLoginToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagicLoginToken query()
 *
 * @mixin \Eloquent
 */
class MagicLoginToken extends Model
{
    protected $fillable = [
        'identifier',
        'issued_token',
        'used',
    ];
}
