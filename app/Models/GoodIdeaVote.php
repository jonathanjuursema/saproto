<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\GoodIdeaVote.
 *
 * @property int $id
 * @property int $user_id
 * @property int $good_idea_id
 * @property int $vote
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read GoodIdea $goodIdea
 *
 * @method static Builder|GoodIdeaVote whereCreatedAt($value)
 * @method static Builder|GoodIdeaVote whereGoodIdeaId($value)
 * @method static Builder|GoodIdeaVote whereId($value)
 * @method static Builder|GoodIdeaVote whereUpdatedAt($value)
 * @method static Builder|GoodIdeaVote whereUserId($value)
 * @method static Builder|GoodIdeaVote whereVote($value)
 * @method static Builder|GoodIdeaVote newModelQuery()
 * @method static Builder|GoodIdeaVote newQuery()
 * @method static Builder|GoodIdeaVote query()
 *
 * @mixin Eloquent
 */
class GoodIdeaVote extends Model
{
    protected $table = 'good_idea_votes';

    protected $guarded = ['id'];

    /** @return HasOne */
    public function goodIdea()
    {
        return $this->hasOne('App\Models\GoodIdea');
    }
}
