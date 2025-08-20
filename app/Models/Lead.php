<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $lead_id
 * @property string $main_lead
 * @property string|null $sub_lead
 * @property string|null $type
 * @property int $is_online
 * @property string|null $description
 * @property int $score
 * @property int|null $department_id
 * @property string|null $color_code
 * @property string|null $note
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\UserClient> $client
 * @property-read int|null $client_count
 * @property-read Collection<int, \App\Models\ClientEvent> $clientEvent
 * @property-read int|null $client_event_count
 * @property-read Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read \App\Models\Department|null $department
 * @property-read mixed $department_name
 * @property-read mixed $lead_name
 * @property-read \App\Models\Department|null $mainLead
 *
 * @method static \Database\Factories\LeadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereMainLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereSubLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Lead extends Model
{
    use HasFactory;

    protected $table = 'tbl_lead';

    protected $primaryKey = 'lead_id';

    protected $keyType = 'string';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lead_id',
        'main_lead',
        'sub_lead',
        'score',
        'department_id',
        'color_code',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->color_code = self::getColorCodeAttribute();
        });
    }

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_lead', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_lead', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = parent::create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_lead', 'channel_datatable'));

        return $model;
    }

    public static function getColorCodeAttribute()
    {
        return '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    protected function departmentName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->department_id !== null ? $this->department->dept_name : null
        );
    }

    public function leadName(): Attribute
    {
        if ($this->sub_lead != null) {

            return Attribute::make(
                get: fn ($value) => $this->main_lead.' : '.$this->sub_lead,
            );
        }

        return Attribute::make(
            get: fn ($value) => $this->main_lead,
        );

    }

    public static function whereLeadId($id)
    {
        if (is_array($id) && empty($id)) {
            return collect(); // empty collection
        }

        return static::query()->where('lead_id', $id)->first();
    }

    public static function whereLeadName($name)
    {
        if (is_array($name) && empty($name)) {
            return collect();
        }

        return static::query()->whereRaw('lower(main_lead) = ?', [strtolower($name)])->first();
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'lead_id', 'lead_id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'lead_id', 'lead_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'lead_id', 'lead_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function mainLead()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
