<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'tbl_prog';
    protected $primaryKey = 'prog_id';

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


    # relation
    public function schoolProgram()
    {
        return $this->hasMany(SchoolProgram::class, 'prog_id', 'prog_id');
    }
}
