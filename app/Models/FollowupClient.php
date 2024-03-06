<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupClient extends Model
{
    use HasFactory;

    protected $table = 'tbl_followup_client';

    protected $fillable = [
        'notes',
        'status',
        'reminder_is_sent'
    ];

    public function clientFollowedUpSchedule()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function picFollowedUpSchedule()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
