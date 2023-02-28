<?php

namespace App\Models;

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

    # relation
    // public function user_roles()
    // {
    //     return $this->hasMany(UserRole::class, 'department_id', 'id');
    // }

    public function purchase_request()
    {
        return $this->hasMany(PurchaseRequest::class, 'purchase_department', 'id');
    }

    public function menu_detail()
    {
        return $this->belongsTo(MenuDetail::class, 'department_id', 'id');
    }
}
