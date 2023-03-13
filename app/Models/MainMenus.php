<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainMenus extends Model
{
    use HasFactory;

    protected $table = 'tbl_main_menus';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'mainmenu_name',
        'order_no',
        'icon',
    ];

    public function submenu()
    {
        return $this->hasMany(Menu::class, 'mainmenu_id', 'id');
    }
}
