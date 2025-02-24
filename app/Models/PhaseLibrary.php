<?php

namespace App\Models;

use App\Models\pivot\ClientProgramDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseLibrary extends Model
{
    use HasFactory;

    protected $table = 'phase_libraries';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'nation',
        'grade',
        'slot'
    ];

    public function phase_detail()
    {
        return $this->belongsTo(PhaseDetail::class, 'phase_detail_id', 'id');
    }

    public function client_program()
    {
        return $this->belongsToMany(ClientProgram::class, 'client_program_details', 'phase_lib_id', 'clientprog_id')->using(ClientProgramDetail::class);
    }
}
