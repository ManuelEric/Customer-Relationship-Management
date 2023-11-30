<?php

namespace App\Models;

use App\Models\SchoolVisit;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tbl_sch';
    protected $primaryKey = 'sch_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sch_id',
        'uuid',
        'sch_name',
        'sch_type',
        'sch_mail',
        'sch_phone',
        'sch_insta',
        'sch_city',
        'sch_location',
        'sch_score',
        'status',
        'is_verified'
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public static function whereSchoolId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('sch_id', $id)->first();
    }

    public static function whereSchoolName($name)
    {
        if (is_array($name) && empty($name)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->whereRaw('lower(sch_name) = ?', [$name])->first();
    }

    # Scopes
    public function scopeIsVerified($query)
    {
        return $query->where('is_verified', 'Y');
    }

    public function scopeIsNotVerified($query)
    {
        return $query->where('is_verified', 'N');
    }

    # relation
    public function detail()
    {
        return $this->hasMany(SchoolDetail::class, 'sch_id', 'sch_id');
    }

    public function aliases()
    {
        return $this->hasMany(SchoolAliases::class, 'sch_id', 'sch_id');
    }

    public function edufair()
    {
        return $this->hasMany(EdufLead::class, 'sch_id', 'sch_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'tbl_sch_event', 'sch_id', 'event_id');
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'sch_id', 'sch_id');
    }

    public function schoolProgram()
    {
        return $this->hasMany(SchoolProgram::class, 'sch_id', 'sch_id');
    }

    public function curriculum()
    {
        return $this->belongsToMany(Curriculum::class, 'tbl_sch_curriculum', 'sch_id', 'curriculum_id')->withTimestamps();
    }

    public function visit()
    {
        return $this->hasMany(SchoolVisit::class, 'sch_id', 'sch_id');
    }

    public function asCollaboratorInPartnerProgram()
    {
        return $this->belongsToMany(PartnerProg::class, 'tbl_partner_prog_sch', 'sch_id', 'partnerprog_id');
    }
    
    public function asCollaboratorInSchoolProgram()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_sch_prog_school', 'sch_id', 'schprog_id');
    }
}
