<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $table = 'tbl_major';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'active',
    ];

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_major', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_major', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_major', 'channel_datatable'));

        return $model;
    }


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

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_dreams_major', 'major_id', 'client_id');
    }
}
