<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupClient extends Model
{
    use HasFactory;

    protected $table = 'tbl_followup_client';

    protected $fillable = [
        'client_id',
        'user_id',
        'notes',
        'minutes_of_meeting',
        'status',
        'reminder_is_sent',
        'followup_date',
    ];

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
