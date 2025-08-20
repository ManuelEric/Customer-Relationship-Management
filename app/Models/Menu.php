<?php

namespace App\Models;

use App\Models\pivot\MenuDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $mainmenu_id
 * @property string $submenu_name
 * @property string $submenu_link
 * @property int $order_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read MenuDetail|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> $department
 * @property-read int|null $department_count
 * @property-read \App\Models\MainMenus $mainmenu
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereMainmenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSubmenuLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSubmenuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use HasFactory;

    protected $table = 'tbl_menus';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'menus_mainmenu',
        'menus_menu',
        'menus_link',
        'menus_icon',
    ];

    // Modify methods Model
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
