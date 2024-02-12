<?php

namespace App\Models;

use App\Models\pivot\MenuDetail;
use App\Models\pivot\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'tbl_department';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'dept_name',
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

    public function scopeWithAndWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
                     ->with([$relation => $constraint]);
    }

    # relation
    // public function user_roles()
    // {
    //     return $this->hasMany(UserRole::class, 'department_id', 'id');
    // }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tbl_user_type_detail', 'department_id', 'user_id')->withTimestamps()->withPivot('status');
    }

    public function purchase_request()
    {
        return $this->hasMany(PurchaseRequest::class, 'purchase_department', 'id');
    }

    public function access_menus()
    {
        // return $this->belongsTo(MenuDetail::class, 'department_id', 'id');
        return $this->belongsToMany(Menu::class, 'tbl_menusdtl', 'department_id', 'menu_id')->using(MenuDetail::class)->withPivot(['copy', 'export'])->withTimestamps();
    }

    public function lead()
    {
        return $this->hasMany(Lead::class, 'department_id', 'id');
    }
}
