<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewClientLead extends Model
{
    use HasFactory;

    protected $table = 'client_lead';

    public function interestPrograms()
    {
        return $this->belongsToMany(Program::class, 'tbl_interest_prog', 'client_id', 'prog_id')->withTimestamps();
    }

    public function leadStatus()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_client_lead_tracking', 'client_id', 'initialprogram_id')->withPivot(['id', 'group_id', 'client_id', 'initialprogram_id', 'type', 'total_result', 'status', 'reason_id'])->withTimestamps();
    }
}
