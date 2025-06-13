<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagicLoginToken extends Model
{
    protected $fillable = [
        'identifier',
        'issued_token',
        'used',
    ];
}
