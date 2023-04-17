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

    protected $primaryKey = 'univ_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'univ_id',
        'univ_name',
        'tag',
        'univ_address',
        'univ_country',
        'univ_email',
        'univ_phone',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

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

    public function pic()
    {
        return $this->hasMany(UniversityPic::class, 'univ_id', 'univ_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'tbl_univ_event', 'univ_id', 'event_id');
    }

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_dreams_uni', 'univ_id', 'client_id');
    }

    public function tags()
    {
        return $this->belongsTo(Tag::class, 'tag', 'id');
    }
}
