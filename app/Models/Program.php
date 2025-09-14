<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $prog_id
 * @property int|null $main_prog_id
 * @property int|null $sub_prog_id
 * @property string|null $prog_program
 * @property string|null $prog_type
 * @property string $prog_mentor
 * @property string $prog_payment
 * @property string|null $prog_scope
 * @property int $active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Collection<int, \App\Models\UserClient> $client
 * @property-read int|null $client_count
 * @property-read Collection<int, \App\Models\ViewClientLead> $clientLead
 * @property-read int|null $client_lead_count
 * @property-read Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read \App\Models\MainProg|null $main_prog
 * @property-read mixed $prog_sub
 * @property-read mixed $program_name
 * @property-read Collection<int, \App\Models\SchoolProgram> $schoolProgram
 * @property-read int|null $school_program_count
 * @property-read Collection<int, \App\Models\SeasonalProgram> $seasonalProgram
 * @property-read int|null $seasonal_program_count
 * @property-read \App\Models\SubProg|null $sub_prog
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program admissionProgList()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program competitionProgList()
 * @method static \Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program sATACTProgList()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program skillsetTutoringProgList()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program subjectTutoringProgList()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program tutoringProgList()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereMainProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereProgMentor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereProgPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereProgProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereProgScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereProgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereSubProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Program extends Model
{
    use HasFactory;

    protected $table = 'tbl_prog';

    protected $primaryKey = 'prog_id';

    protected $appends = ['program_name'];

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prog_id',
        'main_prog_id',
        'sub_prog_id',
        'prog_main',
        'main_number',
        'prog_sub',
        'prog_program',
        'prog_type',
        'prog_mentor',
        'prog_payment',
        'prog_scope',
        'program_name',
        'active', // active status
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_program', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_program', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_program', 'channel_datatable'));

        return $model;
    }

    public static function whereProgId($id)
    {
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('prog_id', $id)->first();
    }

    public function progSub(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == null ? '-' : $value,
        );
    }

    public function progProgram(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == null ? '-' : $value,
        );
    }

    public function programName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sub_prog && $this->main_prog->prog_name !== $this->sub_prog->sub_prog_name
                ? $this->main_prog->prog_name.' - '.$this->sub_prog->sub_prog_name.': '.$this->prog_program
                : $this->main_prog->prog_name.': '.$this->prog_program,
        );

    }

    // relation
    public function schoolProgram()
    {
        return $this->hasMany(SchoolProgram::class, 'prog_id', 'prog_id');
    }

    public function main_prog()
    {
        return $this->belongsTo(MainProg::class, 'main_prog_id', 'id');
    }

    public function sub_prog()
    {
        return $this->belongsTo(SubProg::class, 'sub_prog_id', 'id');
    }

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_interest_prog', 'prog_id', 'client_id');
    }

    public function clientLead()
    {
        return $this->belongsToMany(ViewClientLead::class, 'tbl_interest_prog', 'prog_id', 'client_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'prog_id', 'prog_id');
    }

    public function seasonalProgram()
    {
        return $this->hasMany(SeasonalProgram::class, 'prog_id', 'prog_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeAdmissionProgList($query)
    {
        return $query->whereHas('main_prog', function ($query) {
            $query->where('prog_name', 'Admissions Mentoring');
        })->orWhereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'Admissions Mentoring');
        });
    }

    public function scopeTutoringProgList($query)
    {
        return $query->whereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'like', '%Tutoring%')->orWhere('sub_prog_name', 'like', '%Competition%');
        })->orWhereRelation('main_prog', 'prog_name', 'Test Preparation')->whereNotIn('prog_id', ['SATPREP', 'SATPRO', 'SATINT', 'SATCORE']);
    }

    public function scopeSubjectTutoringProgList($query)
    {
        return $query->whereRelation('main_prog', 'prog_name', 'Subject Tutoring');
    }

    public function scopeCompetitionProgList($query)
    {
        return $query->whereRelation('main_prog', 'prog_name', 'Competition');
    }

    public function scopeSkillsetTutoringProgList($query)
    {
        return $query->whereRelation('main_prog', 'prog_name', 'Skillset Tutoring');
    }

    public function scopeSATACTProgList($query)
    {
        return $query->where('prog_program', 'like', '%SAT%')->orWhereHas('sub_prog', function ($query) {
            $query->where('sub_prog_name', 'like', '%SAT%')->orWhere('sub_prog_name', 'like', '%ACT%');
        });
    }
}
