<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClientAssessment extends Model
{
    use HasFactory;

    protected $connection = 'mysql_assessment';

    protected $table = 'users';
    protected $primaryKey = 'id';
    
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'uuid',
        'uuid_crm',
        'ticket_id',
        'full_name',
        'email',
        'phone_number',
        'state',
        'city',
        'address',
        'school',
        'destination',
        'is_vip',
        'took_ia',
        'took_quest',
        'type',
        'password',
        'created_at',
        'updated_at',
    ];
}
