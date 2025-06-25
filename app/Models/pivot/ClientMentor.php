<?php

namespace App\Models\pivot;

use App\Models\User;
use App\Observers\ClientMentorObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[ObservedBy([ClientMentorObserver::class])]
class ClientMentor extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_client_mentor';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'clientprog_id',
        'user_id', 
        'timesheet_link',
        'type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
