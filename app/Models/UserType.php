<?php

namespace App\Models;

use App\Models\pivot\UserTypeDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_type';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'type_name',
        'status',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_type_detail', 'user_type_id', 'user_id')->using(UserTypeDetail::class);
    }
}
