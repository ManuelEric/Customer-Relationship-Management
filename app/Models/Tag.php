<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tbl_tag';

    protected $fillable = [
        'name'
    ];

    public function universities()
    {
        return $this->hasMany(University::class, 'tag', 'id');
    }

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_abrcountry', 'tag_id', 'client_id');
    }
}
