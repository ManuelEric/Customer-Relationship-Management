<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property string $univ_id
 * @property string|null $univ_name
 * @property string|null $univ_address
 * @property int|null $univ_country
 * @property string|null $univ_email
 * @property string|null $univ_phone
 * @property string|null $early_action Early application (early action)
 * @property string|null $early_decision Early application (early decision)
 * @property string|null $regular_deadline Regular Deadline
 * @property string|null $univ_requirement_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PartnerProg> $asCollaboratorInPartnerProgram
 * @property-read int|null $as_collaborator_in_partner_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolProg> $asCollaboratorInSchoolProgram
 * @property-read int|null $as_collaborator_in_school_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClient> $client
 * @property-read int|null $client_count
 * @property-read \App\Models\UnivCountry|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UniversityPic> $pic
 * @property-read int|null $pic_count
 * @property-read \App\Models\MasterCountry|null $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $trackedUniversityAcceptanceFromClient
 * @property-read int|null $tracked_university_acceptance_from_client_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClient> $trackedUniversityAcceptanceFromUserClient
 * @property-read int|null $tracked_university_acceptance_from_user_client_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereEarlyAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereEarlyDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereRegularDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUnivRequirementLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|University withAndWhereHas($relation, $constraint)
 *
 * @mixin \Eloquent
 */
class University extends Model
{
    use HasFactory;

    protected $table = 'tbl_univ';

    protected $primaryKey = 'univ_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'univ_id',
        'univ_name',
        // 'tag',
        'univ_address',
        'univ_country',
        'univ_email',
        'univ_phone',
        'early_action',
        'early_decision',
        'regular_deadline',
    ];

    // Modify methods Model
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
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('univ_id', $id)->first();
    }

    protected function univAddress(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::trim(strip_tags($value)),
        );
    }

    // helper
    public static function trim($string)
    {
        return $string = trim(preg_replace('/\s\s+/', ' ', $string));
    }

    /**
     * Scopes
     */
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function scopeSearch($query, $search)
    {
        $terms = $search['terms'] ?? null;
        $query->when($terms, function ($query) use ($terms) {
            $query->where('univ_name', 'like', '%'.$terms.'%');
        });
    }

    /**
     * Relationships
     */
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
        return $this->belongsTo(MasterCountry::class, 'univ_country', 'id');
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

    public function country()
    {
        return $this->belongsTo(UnivCountry::class, 'univ_country', 'id');
    }
}
