<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Editor extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_editor';
    protected $primaryKey = 'editor_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'editor_id',
        'editor_fn',
        'editor_ln',
        'editor_address',
        'editor_major',
        'univ_id',
        'editor_mail',
        'editor_phone',
        'editor_position',
        'editor_passw',
        'editor_cv',
        'editor_ktp',
        'editor_bankname',
        'editor_bankacc',
        'editor_npwp',
        'editor_status',
        'editor_feephours',
        'editor_lastcontact',
        'editor_notes',
        'editor_lastupdate',
    ];

    protected function editorMajor(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => str_contains($value, "-") ? self::removeSeparator("-", $value) : $value ,
        );
    }

    public static function removeSeparator($separator, $value)
    {
        return str_replace($separator, '', $value);
    }

    # relation
    public function university()
    {
        return $this->belongsTo(University::class, 'univ_id', 'univ_id');
    }
}
