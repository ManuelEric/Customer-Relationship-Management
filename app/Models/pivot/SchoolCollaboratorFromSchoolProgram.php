<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $schprog_id
 * @property string $sch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram whereSchprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolCollaboratorFromSchoolProgram whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolCollaboratorFromSchoolProgram extends Pivot
{
    protected $table = 'tbl_sch_prog_school';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'schprog_id',
        'sch_id',
    ];
}
