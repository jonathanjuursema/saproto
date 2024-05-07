<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\FeedbackVote.
 *
 * @property int $id
 * @property int $user_id
 * @property int $feedback_id
 * @property int $vote
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Feedback $feedback
 *
 * @method static Builder|FeedbackVote whereCreatedAt($value)
 * @method static Builder|FeedbackVote whereFeedbackId($value)
 * @method static Builder|FeedbackVote whereId($value)
 * @method static Builder|FeedbackVote whereUpdatedAt($value)
 * @method static Builder|FeedbackVote whereUserId($value)
 * @method static Builder|FeedbackVote whereVote($value)
 * @method static Builder|FeedbackVote newModelQuery()
 * @method static Builder|FeedbackVote newQuery()
 * @method static Builder|FeedbackVote query()
 *
 * @mixin Eloquent
 */
class FeedbackVote extends Model
{
    protected $table = 'feedback_votes';

    protected $guarded = ['id'];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Feedback::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(\App\Models\User::class);
    }
}
