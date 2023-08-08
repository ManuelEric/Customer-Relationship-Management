<?php

namespace App\Models;

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
