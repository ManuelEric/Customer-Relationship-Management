<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'tbl_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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

    # Modify methods Model
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

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_lead', 'channel_datatable'));

        return $model;
    }

    public static function getColorCodeAttribute()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
    
    protected function departmentName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->department_id !== null ? $this->department->dept_name : null
        );
    }

    public function leadName(): Attribute
    {
        if ($this->sub_lead != null) {

            return Attribute::make(
                get: fn ($value) => $this->main_lead . ' : ' . $this->sub_lead,
            );
        }
            
            
        return Attribute::make(
            get: fn ($value) => $this->main_lead,
        );
        
    }

    public static function whereLeadId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('lead_id', $id)->first();
    }

    public static function whereLeadName($name)
    {
        if (is_array($name) && empty($name)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->whereRaw('lower(main_lead) = ?', [$name])->first();
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
