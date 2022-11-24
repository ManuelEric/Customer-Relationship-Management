<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\pivot\AssetReturned;
use App\Models\pivot\AssetUsed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'extended_id',
        'first_name',
        'last_name',
        'address',
        'email',
        'phone',
        'emergency_contact',
        'datebirth',
        'position_id',
        'password',
        'hiredate',
        'nik',
        'idcard',
        'cv',
        'bankname',
        'bankacc',
        'npwp',
        'tax',
        'active',
        'health_insurance',
        'empl_insurance',
        'export',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function whereExtendedId($id)
    {
        if (is_array($id) && empty($id)) return new Collection();
        
        $instance = new static;

        return $instance->newQuery()->where('extended_id', $id)->first();
    }

    public static function whereFullName($name)
    {
        if (is_array($name) && empty($name)) return new Collection();
        
        $instance = new static;

        return $instance->newQuery()->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%'.$name.'%'])->first();
    }

    # relation
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_user_roles', 'user_id', 'role_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function educations()
    {
        return $this->belongsToMany(University::class, 'tbl_user_educations', 'user_id', 'univ_id')
                    ->withPivot('major_id')
                    ->join('tbl_major', 'major_id', '=', 'tbl_major.id');
    }

    public function assetUsed()
    {
        return $this->belongsToMany(Asset::class, 'tbl_asset_used', 'user_id', 'asset_id')->using(AssetUsed::class)->withPivot(
            [
                'id',
                'used_date',
                'amount_used',
                'condition',
            ]
        );
        // return $this->belongsToMany(Asset::class, 'tbl_asset_used', 'user_id', 'asset_id');
    }

    public function edufairReview()
    {
        return $this->hasMany(EdufReview::class, 'reviewer_name', 'id');
    }

    public function handledEvent()
    {
        return $this->belongsToMany(Event::class, 'tbl_event_pic', 'empl_id', 'event_id');
    }
}
