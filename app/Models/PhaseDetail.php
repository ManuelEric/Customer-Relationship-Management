<?php

namespace App\Models;

use App\Models\pivot\ClientProgramDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseDetail extends Model
{
    use HasFactory;

    protected $table = 'phase_details';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'phase_detail_name'
    ];

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id', 'id');
    }

    public function phase_libraries()
    {
        return $this->hasMany(PhaseLibrary::class, 'phase_detail_id', 'id');
    }

    public function client_program()
    {
        return $this->belongsToMany(ClientProgram::class, 'client_program_details', 'phase_detail_id', 'clientprog_id')->using(ClientProgramDetail::class)->withPivot('quota', 'use');
    }
}
