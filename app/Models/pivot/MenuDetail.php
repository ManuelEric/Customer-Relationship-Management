<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $menusdtl_id
 * @property int $menu_id
 * @property int $department_id
 * @property int $copy
 * @property int $export
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereCopy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereExport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereMenusdtlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MenuDetail extends Pivot
{
    protected $table = 'tbl_menusdtl';

    protected $primaryKey = 'menusdtl_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
