<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string|null $id
 * @property string $fullname
 * @property string $fname
 * @property string $mname
 * @property string $lname
 * @property string|null $suggestion
 * @property string|null $mail
 * @property string|null $phone
 * @property string|null $is_verifiedsecond_client
 * @property string|null $second_client_id
 * @property string|null $second_client_name
 * @property string|null $second_client_mail
 * @property string|null $second_client_phone
 * @property string|null $second_school_name
 * @property string|null $is_verifiedsecond_school
 * @property string|null $scholarship Scholarship Eligibility
 * @property int|null $second_client_grade_now
 * @property string|null $second_client_year_gap
 * @property int|null $second_client_graduation_year_now
 * @property string|null $second_client_interest_countries
 * @property string|null $second_client_joined_event
 * @property string|null $second_client_interest_prog
 * @property string|null $second_client_created_at
 * @property int|null $second_client_statusact status aktif client
 * @property int|null $real_grade
 * @property int|null $year_gap
 * @property int|null $graduation_year_now
 * @property string|null $graduation_year
 * @property string|null $lead_id
 * @property string|null $lead_source
 * @property string|null $referral_name
 * @property string|null $sch_id
 * @property string|null $interest_countries
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property string|null $school_name
 * @property string|null $is_verifiedschool
 * @property int|null $count_second_client
 * @property string|null $joined_event
 * @property string|null $interest_prog
 * @property string|null $pic
 * @property string|null $pic_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientEvent> $clientEvent
 * @property-read int|null $client_event_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientEvent> $clientEventAsChild
 * @property-read int|null $client_event_as_child_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $destinationCountries
 * @property-read int|null $destination_countries_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereCountSecondClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereFname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereGraduationYearNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereInterestCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereInterestProg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereIsVerifiedschool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereIsVerifiedsecondClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereIsVerifiedsecondSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereJoinedEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereLeadSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereLname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereMname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient wherePic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient wherePicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereRealGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereReferralName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereScholarship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientGradeNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientGraduationYearNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientInterestCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientInterestProg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientJoinedEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientStatusact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondClientYearGap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSecondSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereSuggestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewRawClient whereYearGap($value)
 *
 * @mixin \Eloquent
 */
class ViewRawClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'raw_client';

    protected $keyType = 'string';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'fullname',
        'fname',
        'mname',
        'lname',
        'mail',
        'phone',
        'second_client_name',
        'second_client_mail',
        'second_client_phone',
        'school',
        'register_by',
        'sch_id',
        'interest_countries',
        'lead_source',
        'graduation_year_now',
        'grade_now',
        'created_at',
        'updated_at',
        'roles',
        'lead_id',
        'scholarship',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'client_id', 'id');
    }

    public function clientEventAsChild()
    {
        return $this->hasMany(ClientEvent::class, 'child_id', 'id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'client_id', 'id');
    }

    public function destinationCountries()
    {
        return $this->belongsToMany(Tag::class, 'tbl_client_abrcountry', 'client_id', 'country_id')->withTimestamps();
    }
}
