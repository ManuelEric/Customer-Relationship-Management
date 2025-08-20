<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $sch_id
 * @property string $uuid
 * @property string|null $sch_name
 * @property string|null $sch_type
 * @property string|null $sch_mail
 * @property string|null $sch_phone
 * @property string|null $sch_insta
 * @property string|null $sch_city
 * @property string|null $sch_location
 * @property int $sch_score
 * @property int $status
 * @property string $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\SchoolAliases> $aliases
 * @property-read int|null $aliases_count
 * @property-read Collection<int, \App\Models\PartnerProg> $asCollaboratorInPartnerProgram
 * @property-read int|null $as_collaborator_in_partner_program_count
 * @property-read Collection<int, \App\Models\SchoolProg> $asCollaboratorInSchoolProgram
 * @property-read int|null $as_collaborator_in_school_program_count
 * @property-read Collection<int, \App\Models\UserClient> $client
 * @property-read int|null $client_count
 * @property-read Collection<int, \App\Models\Curriculum> $curriculum
 * @property-read int|null $curriculum_count
 * @property-read Collection<int, \App\Models\SchoolDetail> $detail
 * @property-read int|null $detail_count
 * @property-read Collection<int, \App\Models\EdufLead> $edufair
 * @property-read int|null $edufair_count
 * @property-read Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read Collection<int, \App\Models\SchoolProgram> $schoolProgram
 * @property-read int|null $school_program_count
 * @property-read Collection<int, \App\Models\SchoolVisit> $visit
 * @property-read int|null $visit_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School isNotVerified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School isVerified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchInsta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereSchType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School withoutTrashed()
 *
 * @mixin \Eloquent
 */
class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_sch';

    protected $primaryKey = 'sch_id';

    public $incrementing = false;

    public $timestamps = true;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sch_id',
        'uuid',
        'sch_name',
        'sch_type',
        'sch_mail',
        'sch_phone',
        'sch_insta',
        'sch_city',
        'sch_location',
        'sch_score',
        'status',
        'is_verified',
        'created_at',
        'updated_at',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_school', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_school', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_school', 'channel_datatable'));

        return $model;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public static function whereSchoolId($id)
    {
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('sch_id', $id)->first();
    }

    public static function whereSchoolName($name)
    {
        if (is_array($name) && empty($name)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->whereRaw('lower(sch_name) = ?', [$name])->first();
    }

    // Scopes
    public function scopeIsVerified($query)
    {
        return $query->where('is_verified', 'Y');
    }

    public function scopeIsNotVerified($query)
    {
        return $query->where('is_verified', 'N');
    }

    // relation
    public function detail()
    {
        return $this->hasMany(SchoolDetail::class, 'sch_id', 'sch_id');
    }

    public function aliases()
    {
        return $this->hasMany(SchoolAliases::class, 'sch_id', 'sch_id');
    }

    public function edufair()
    {
        return $this->hasMany(EdufLead::class, 'sch_id', 'sch_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'tbl_sch_event', 'sch_id', 'event_id');
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'sch_id', 'sch_id');
    }

    public function schoolProgram()
    {
        return $this->hasMany(SchoolProgram::class, 'sch_id', 'sch_id');
    }

    public function curriculum()
    {
        return $this->belongsToMany(Curriculum::class, 'tbl_sch_curriculum', 'sch_id', 'curriculum_id')->withTimestamps();
    }

    public function visit()
    {
        return $this->hasMany(SchoolVisit::class, 'sch_id', 'sch_id');
    }

    public function asCollaboratorInPartnerProgram()
    {
        return $this->belongsToMany(PartnerProg::class, 'tbl_partner_prog_sch', 'sch_id', 'partnerprog_id');
    }

    public function asCollaboratorInSchoolProgram()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_sch_prog_school', 'sch_id', 'schprog_id');
    }
}
