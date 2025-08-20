<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $programbucket_id
 * @property string|null $leadbucket_id
 * @property int $value_category
 * @property int $new
 * @property int $existing_mentee
 * @property int $existing_non_mentee
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereExistingMentee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereExistingNonMentee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereLeadbucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereProgrambucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramLeadLibrary whereValueCategory($value)
 *
 * @mixin \Eloquent
 */
class ProgramLeadLibrary extends Model
{
    use HasFactory;

    protected $table = 'tbl_program_lead_library';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'programbucket_id',
        'leadbucket_id',
        'value_category',
        'new',
        'existing_mentee',
        'existing_non_mentee',
    ];
}
