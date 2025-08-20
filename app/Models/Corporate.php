<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $corp_id
 * @property string|null $corp_name
 * @property string|null $user_id
 * @property int|null $corp_industry
 * @property int|null $corp_subsector_id
 * @property string|null $corp_mail
 * @property string|null $corp_phone
 * @property string|null $corp_insta
 * @property string|null $corp_site
 * @property string|null $corp_region
 * @property string|null $corp_city
 * @property string|null $corp_address
 * @property string|null $corp_note
 * @property string|null $corp_password
 * @property string|null $country_type
 * @property string|null $type
 * @property string|null $partnership_type
 * @property int $active_status
 * @property string|null $corp_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\PartnerProg> $asCollaboratorInPartnerProgram
 * @property-read int|null $as_collaborator_in_partner_program_count
 * @property-read Collection<int, \App\Models\SchoolProg> $asCollaboratorInSchoolProgram
 * @property-read int|null $as_collaborator_in_school_program_count
 * @property-read Collection<int, \App\Models\ClientEvent> $clientEvent
 * @property-read int|null $client_event_count
 * @property-read Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read Collection<int, \App\Models\EdufLead> $edufair
 * @property-read int|null $edufair_count
 * @property-read Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \App\Models\User|null $individualProfessional
 * @property-read \App\Models\Industry|null $industry
 * @property-read mixed $partner_name
 * @property-read Collection<int, \App\Models\PartnerProg> $partnerProgram
 * @property-read int|null $partner_program_count
 * @property-read Collection<int, \App\Models\CorporatePic> $pic
 * @property-read int|null $pic_count
 * @property-read Collection<int, \App\Models\Referral> $referralProgram
 * @property-read int|null $referral_program_count
 * @property-read \App\Models\SubSector|null $subSector
 *
 * @method static Builder<static>|Corporate active()
 * @method static Builder<static>|Corporate newModelQuery()
 * @method static Builder<static>|Corporate newQuery()
 * @method static Builder<static>|Corporate query()
 * @method static Builder<static>|Corporate whereActiveStatus($value)
 * @method static Builder<static>|Corporate whereCorpAddress($value)
 * @method static Builder<static>|Corporate whereCorpCity($value)
 * @method static Builder<static>|Corporate whereCorpId($value)
 * @method static Builder<static>|Corporate whereCorpIndustry($value)
 * @method static Builder<static>|Corporate whereCorpInsta($value)
 * @method static Builder<static>|Corporate whereCorpMail($value)
 * @method static Builder<static>|Corporate whereCorpName($value)
 * @method static Builder<static>|Corporate whereCorpNote($value)
 * @method static Builder<static>|Corporate whereCorpPassword($value)
 * @method static Builder<static>|Corporate whereCorpPhone($value)
 * @method static Builder<static>|Corporate whereCorpRegion($value)
 * @method static Builder<static>|Corporate whereCorpSite($value)
 * @method static Builder<static>|Corporate whereCorpStatus($value)
 * @method static Builder<static>|Corporate whereCorpSubsectorId($value)
 * @method static Builder<static>|Corporate whereCountryType($value)
 * @method static Builder<static>|Corporate whereCreatedAt($value)
 * @method static Builder<static>|Corporate wherePartnershipType($value)
 * @method static Builder<static>|Corporate whereType($value)
 * @method static Builder<static>|Corporate whereUpdatedAt($value)
 * @method static Builder<static>|Corporate whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Corporate extends Model
{
    use HasFactory;

    protected $table = 'tbl_corp';

    protected $primaryKey = 'corp_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'corp_id',
        'corp_name',
        'user_id',
        'corp_industry',
        'corp_subsector_id',
        'corp_mail',
        'corp_phone',
        'corp_insta',
        'corp_site',
        'corp_region',
        'corp_address',
        'corp_note',
        'corp_password',
        'country_type',
        'corp_status',
        'active_status',
        'type',
        'partnership_type',
        'corp_city',
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
        event(new MessageSent('rt_partner', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_partner', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_partner', 'channel_datatable'));

        return $model;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    protected function partnerName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->type == 'Individual Professional' && $this->user_id != null ? $this->individualProfessional->full_name : $this->corp_name
        );
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('active_status', 1);
    }

    public static function whereCorpId($id)
    {
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('corp_id', $id)->first();
    }

    public static function whereCorpName($name)
    {
        if (is_array($name) && empty($name)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->whereRaw('lower(corp_name) = ?', [$name])->first();
    }

    public function edufair()
    {
        return $this->hasMany(EdufLead::class, 'corp_id', 'corp_id');
    }

    public function pic()
    {
        return $this->hasMany(CorporatePic::class, 'corp_id', 'corp_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'tbl_corp_partner_event', 'corp_id', 'event_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'partner_id', 'corp_id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'partner_id', 'corp_id');
    }

    public function partnerProgram()
    {
        return $this->hasMany(PartnerProg::class, 'corp_id', 'corp_id');
    }

    public function referralProgram()
    {
        return $this->hasMany(Referral::class, 'partner_id', 'corp_id');
    }

    public function asCollaboratorInPartnerProgram()
    {
        return $this->belongsToMany(PartnerProg::class, 'tbl_partner_prog_partner', 'corp_id', 'partnerprog_id')->withTimestamps();
    }

    public function asCollaboratorInSchoolProgram()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_sch_prog_partner', 'corp_id', 'schprog_id')->withTimestamps();
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class, 'corp_industry', 'id');
    }

    public function subSector()
    {
        return $this->belongsTo(SubSector::class, 'corp_subsector_id', 'id');
    }

    public function individualProfessional()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
