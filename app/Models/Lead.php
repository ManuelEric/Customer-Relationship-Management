<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'tbl_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'lead_id',
        'main_lead',
        'sub_lead',
        'score',
    ];

    public static function whereLeadId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('lead_id', $id)->first();
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'lead_id', 'lead_id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'lead_id', 'lead_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'lead_id', 'lead_id');
    }
}
