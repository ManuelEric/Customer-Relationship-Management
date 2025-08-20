<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $prog_id
 * @property string|null $start
 * @property string|null $end
 * @property string|null $sales_date The date when sales department can start selling
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $end_string
 * @property-read mixed $initial_program
 * @property-read \App\Models\ViewProgram $program
 * @property-read mixed $sales_date_string
 * @property-read mixed $start_string
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereSalesDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeasonalProgram withAndWhereHas($relation, $constraint)
 *
 * @mixin \Eloquent
 */
class SeasonalProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_seasonal_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prog_id',
        'start',
        'end',
        'sales_date',
    ];

    // Modify methods Model
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
            get: fn ($value) => $this->program->sub_prog, // need to add specific concern / initial program
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
