<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    public function submenu()
    {
        return $this->hasMany(Menu::class, 'mainmenu_id', 'id');
    }
}
