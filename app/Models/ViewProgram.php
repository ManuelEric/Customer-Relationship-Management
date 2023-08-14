<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewProgram extends Model
{
    use HasFactory;

    protected $table = 'program';
    // protected $primaryKey = 'prog_id';

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function sub_prog()
    {
        return $this->belongsTo(SubProg::class, 'sub_prog_id', 'id');
    }
    
    public function seasonalProgram()
    {
        return $this->hasMany(SeasonalProgram::class, 'prog_id', 'prog_id');
    }
}
