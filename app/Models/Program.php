<?php

namespace App\Models;

use App\Observers\ProgramObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ProgramObserver::class])]
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
     * @var array
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
        'active', # active status
    ];

    public static function whereProgId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('prog_id', $id)->first();
    }

    public function progSub(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == NULL ? "-" : $value,
        );
    }

    public function progProgram(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == NULL ? "-" : $value,
        );
    }

    public function programName(): Attribute
    {
        if ($this->sub_prog != null) {

            if ($this->main_prog->prog_name == $this->sub_prog->sub_prog_name) {
                return Attribute::make(
                    get: fn ($value) => $this->main_prog->prog_name . ' : ' . $this->prog_program,
                );
            }
            return Attribute::make(
                get: fn ($value) => $this->main_prog->prog_name . ' / ' . $this->sub_prog->sub_prog_name . ' : ' . $this->prog_program,
            );
        } else {
            return Attribute::make(
                get: fn ($value) => $this->main_prog->prog_name . ' : ' . $this->prog_program,
            );
        }
    }

    # relation
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
}
