<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'tbl_menus';
    protected $primaryKey = 'menus_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'menus_mainmenu',
        'menus_menu',
        'menus_link',
        'menus_icon',
    ];

    public function menu_detail()
    {
        return $this->hasMany(MenuDetail::class, 'menus_id', 'menus_id');
    }
}
