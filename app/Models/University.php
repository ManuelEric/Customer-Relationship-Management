<?php

namespace App\Models;

use App\Events\MessageSent;
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

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_university', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_university', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_university', 'channel_datatable'));

        return $model;
    }

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

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
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

    public function asCollaboratorInPartnerProgram()
    {
        return $this->belongsToMany(PartnerProg::class, 'tbl_partner_prog_univ', 'univ_id', 'partnerprog_id');
    }

    public function asCollaboratorInSchoolProgram()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_sch_prog_univ', 'univ_id', 'schprog_id');
    }

    public function trackedUniversityAcceptanceFromUserClient()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_acceptance', 'univ_id', 'client_id')->withPivot('client_id')->withTimestamps();
    }

    public function trackedUniversityAcceptanceFromClient()
    {
        return $this->belongsToMany(Client::class, 'tbl_client_acceptance', 'univ_id', 'client_id');
    }
}
