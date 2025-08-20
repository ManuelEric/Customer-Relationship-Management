<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $eduf_id
 * @property string $reviewer_name
 * @property string $score
 * @property string $review
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EdufLead $edufair
 * @property-read \App\Models\User $reviewer
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereEdufId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereReviewerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdufReview whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class EdufReview extends Model
{
    use HasFactory;

    protected $table = 'tbl_eduf_review';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'eduf_id',
        'reviewer_name',
        'score',
        'review',
    ];

    public function edufair()
    {
        return $this->belongsTo(EdufLead::class, 'eduf_id', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_name', 'id');
    }
}
