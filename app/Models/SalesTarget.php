<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class SalesTarget extends Model
{
    use HasFactory;

    protected $table = 'tbl_sales_target';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'main_prog_id',
        'prog_id',
        'sub_prog_id',
        'month_year',
        'total_participant',
        'total_target',
    ];

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_sales_target', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_sales_target', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_sales_target', 'channel_datatable'));

        return $model;
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function main_program()
    {
        return $this->belongsTo(MainProg::class, 'main_prog_id', 'id');
    }

}
