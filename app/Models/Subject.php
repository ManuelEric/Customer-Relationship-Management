<?php

namespace App\Models;

use App\Models\pivot\UserSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'tbl_subjects';
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name', 
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_subjects', 'subject_id', 'user_id')->using(UserSubject::class)->withPivot('feehours', 'feesession')->withTimestamps();
    }

}
