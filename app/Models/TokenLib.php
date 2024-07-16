<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLib extends Model
{
    use HasFactory;

    protected $table = 'token_lib';

    public $timestamps = false;

    protected $fillable = [
        'header_name',
        'value',
        'expires_at'
    ];
}
