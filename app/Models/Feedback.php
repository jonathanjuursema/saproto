<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Feedback model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $feedback
 * @property User|null $reply
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool|null $reviewed
 * @property bool|null $accepted
 * @property FeedbackCategory|null $category
 * @property-read User|null $user
 * @property-read Collection|FeedbackVote[] $votes
 *
 * @method static Builder|Feedback whereCreatedAt($value)
 * @method static Builder|Feedback whereId($value)
 * @method static Builder|Feedback whereFeedback($value)
 * @method static Builder|Feedback whereUpdatedAt($value)
 * @method static Builder|Feedback whereUserId($value)
 * @method static Builder|Feedback newModelQuery()
 * @method static Builder|Feedback newQuery()
 * @method static Builder|Feedback query()
 *
 * @mixin Eloquent
 */
class Feedback extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'feedback';

    protected $guarded = ['id'];

    protected $with = ['user', 'category'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FeedbackCategory::class, 'feedback_category_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(FeedbackVote::class);
    }

    public function voteScore(): int
    {
        return $this->votes()->sum('vote');
    }

    public function mayViewFeedback($user): bool
    {
        if (! $this->category->review) {
            return true;
        }

        if ($this->reviewed) {
            return true;
        }

        return $this->category->reviewer_id === $user->id;
    }

    public function userVote(User $user): int
    {
        /** @var FeedbackVote $vote */
        $vote = $this->votes()->where('user_id', $user->id)->first();
        if ($vote != null) {
            return $vote->vote;
        }

        return 0;
    }
}
