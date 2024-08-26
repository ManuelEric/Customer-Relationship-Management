<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClientAdditionalInfo extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_additional_info';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'category',
        'value',
        'description'
    ];

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }
}
