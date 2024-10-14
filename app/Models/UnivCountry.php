<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnivCountry extends Model
{
    use HasFactory;

    protected $table = 'tbl_country';

    protected $fillable = [
        'name',
        'tag'
    ];


    /**
     * The relations.
     * 
     */
    public function universities()
    {
        return $this->hasMany(University::class, 'univ_country', 'id');
    }

    public function tags()
    {
        return $this->belongsTo(Tag::class, 'tag', 'id');
    }
}
