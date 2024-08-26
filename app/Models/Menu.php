<?php

namespace App\Models;

use App\Models\pivot\MenuDetail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model

        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update

        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $model;
    }

    public function mainmenu()
    {
        return $this->belongsTo(MainMenus::class, 'mainmenu_id', 'id');
    }

    public function department()
    {
        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        // return $this->hasMany(MenuDetail::class, 'menus_id', 'menus_id');
        return $this->belongsToMany(Department::class, 'tbl_menusdtl', 'menu_id', 'department_id')->using(MenuDetail::class)->withPivot(['copy', 'export'])->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tbl_menus_user', 'menu_id', 'user_id')->withTimestamps();
    }
}
