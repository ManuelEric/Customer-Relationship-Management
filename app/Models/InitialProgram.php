<?php

namespace App\Models;

use App\Models\pivot\ClientLeadTracking;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_initial_program_lead';
    protected $primaryKey = 'id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];


    public function sub_prog()
    {
        return $this->belongsTo(SubProg::class, 'sub_id', 'id');
        // return $this->belongsToMany(SubProg::class, 'tbl_initial_prog_sub_lead', 'initialprogram_id', 'subprogram_id');
    }

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_lead_tracking', 'initialprogram_id', 'client_id')->using(ClientLeadTracking::class)->withTimestamps();
    }
}
