<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MenuDetail extends Pivot
{
    protected $table = 'tbl_menusdtl';
    protected $primaryKey = 'menusdtl_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'menus_id',
        'department_id',
        'copy',
        'export',
    ];

    // public function menu()
    // {
    //     return $this->belongsTo(Menu::class, 'menus_id', 'menus_id');
    // }

    // public function department()
    // {
    //     return $this->hasOne(Department::class, 'id', 'department_id');
    // }
}
