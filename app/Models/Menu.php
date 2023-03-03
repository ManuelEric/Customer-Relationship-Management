<?php

namespace App\Models;

use App\Models\pivot\MenuDetail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'tbl_menus';

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

    public function mainmenu()
    {
        return $this->belongsTo(MainMenus::class, 'mainmenu_id', 'id');
    }

    public function department()
    {
        // return $this->hasMany(MenuDetail::class, 'menus_id', 'menus_id');
        return $this->belongsToMany(Department::class, 'tbl_menusdtl', 'menu_id', 'department_id')->using(MenuDetail::class)->withPivot(['copy', 'export'])->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tbl_menus_user', 'menu_id', 'user_id')->withTimestamps();
    }
}
