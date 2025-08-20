<?php

namespace App\Models\Mentoring;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $student_id
 * @property string|null $mentor_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property float|null $work_duration
 * @property string|null $meeting_notes
 * @property string|null $mentee_to_do_list
 * @property string|null $next_agenda
 * @property string|null $meeting_notes_file
 * @property string|null $support_document
 * @property int $phase_detail_id
 * @property string|null $meeting_link
 * @property string|null $type
 * @property string|null $attachment_link
 * @property int $is_updated_parent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereAttachmentLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereIsUpdatedParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereMeetingLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereMeetingNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereMeetingNotesFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereMenteeToDoList($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereMentorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereNextAgenda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog wherePhaseDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereSupportDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentoringLog whereWorkDuration($value)
 *
 * @mixin \Eloquent
 */
class MentoringLog extends Model
{
    protected $connection = 'mysql_mentoring';

    protected $table = 'mentoring_logs';
}
