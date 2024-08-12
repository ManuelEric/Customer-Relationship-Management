<?php

namespace App\Models;

use App\Observers\CurriculumObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


#[ObservedBy([CurriculumObserver::class])]
class Curriculum extends Model
{
    use HasFactory;
    protected $table = 'tbl_curriculum';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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

    public function school()
    {
        return $this->belongsToMany(School::class, 'tbl_sch_curriculum', 'curriculum_id', 'sch_id');
    }
}
