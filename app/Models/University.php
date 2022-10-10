<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class University extends Model
{
    use HasFactory;

    protected $table = 'tbl_univ';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'univ_id',
        'univ_name', 
        'univ_address', 
        'univ_country',
    ];

    public static function whereUniversityId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->where('univ_id', $id)->first();
    }

    protected function univAddress(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::trim(strip_tags($value)),
        );
    }

    # helper
    public static function trim($string)
    {
        return $string = trim(preg_replace('/\s\s+/', ' ', $string));
    }

    # relation
    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_educations', 'univ_id', 'user_id');
    }
}
