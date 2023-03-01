<?php

namespace App\Models;

use App\Models\pivot\UserTypeDetail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_login_log';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'user_type_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_login_log', 'user_type_id', 'user_id');
    }

    public function user_type()
    {
        return $this->belongsTo(UserTypeDetail::class, 'user_type_id', 'id');
    }
}
