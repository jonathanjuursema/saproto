<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Feedback model.
 *
 * @property int $id
 * @property string $title
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $review
 * @property int $reviewer_id
 * @property bool $can_reply
 * @property bool $show_publisher
 * @property-read User|null $reviewer
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
class FeedbackCategory extends Model
{
    protected $table = 'feedback_categories';

    protected $guarded = ['id'];

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'feedback_category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
