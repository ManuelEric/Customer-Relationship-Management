<?php

namespace App\Models\pivot;

use App\Models\Major;
use App\Models\MajorGroup;
use App\Models\UserClient;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientAcceptance extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_client_acceptance';

    protected $fillable = [
        'client_id',
        'univ_id',
        'major_group_id',
        'major_name',
        'major_id', //! unused since there were a major_group_id
        'category',
        'status',
        'is_picked',
        'requirement_link',
    ];

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id', 'id');
    }

    public function major_group()
    {
        return $this->belongsTo(MajorGroup::class, 'major_group_id', 'id');
    }

    public function getMajorName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                if ( $this->major_name == null && $this->major_id == null )
                    $major = null;
                else
                    $major = $this->major_name ?? $this->major->name;

                return $major;
            }
        );
    }
}
