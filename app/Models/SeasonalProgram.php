<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_seasonal_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'prog_id',
        'start',
        'end',
        'sales_date'
    ];

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_seasonal_program', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_seasonal_program', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_seasonal_program', 'channel_datatable'));

        return $model;
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function initialProgram(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->program->sub_prog, # need to add specific concern / initial program
        );
    }

    public function startString(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($this->start)),
        );
    }

    public function endString(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($this->end)),
        );
    }

    public function salesDateString(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($this->sales_date)),
        );
    }

    public function program()
    {
        return $this->belongsTo(ViewProgram::class, 'prog_id', 'prog_id');
    }
}
