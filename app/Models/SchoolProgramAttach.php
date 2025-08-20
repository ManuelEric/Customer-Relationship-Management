<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $schprog_id
 * @property string $schprog_file
 * @property string $schprog_attach
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SchoolProgram $school_program
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach whereSchprogAttach($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach whereSchprogFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach whereSchprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProgramAttach whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolProgramAttach extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_prog_attach';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'schprog_id',
        'schprog_file',
        'schprog_attach',
    ];

    public function school_program()
    {
        return $this->belongsTo(SchoolProgram::class, 'schprog_id', 'id');
    }
}
