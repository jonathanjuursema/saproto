<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Leaderboard Model.
 *
 * @property int $id
 * @property int $committee_id
 * @property string $name
 * @property string $description
 * @property string $points_name
 * @property string|null $icon
 * @property bool $featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Committee $committee
 * @property-read Collection|LeaderboardEntry[] $entries
 *
 * @method static Builder|Leaderboard whereCommitteeId($value)
 * @method static Builder|Leaderboard whereCreatedAt($value)
 * @method static Builder|Leaderboard whereDescription($value)
 * @method static Builder|Leaderboard whereFeatured($value)
 * @method static Builder|Leaderboard whereIcon($value)
 * @method static Builder|Leaderboard whereId($value)
 * @method static Builder|Leaderboard whereName($value)
 * @method static Builder|Leaderboard wherePointsName($value)
 * @method static Builder|Leaderboard whereUpdatedAt($value)
 * @method static Builder|Leaderboard newModelQuery()
 * @method static Builder|Leaderboard newQuery()
 * @method static Builder|Leaderboard query()
 *
 * @mixin Eloquent
 */
class Leaderboard extends Model
{
    protected $table = 'leaderboards';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committee_id');
    }

    /** @return HasMany */
    public function entries()
    {
        return $this->hasMany(LeaderboardEntry::class);
    }

    public static function isAdminAny(User $user): bool
    {
        return \App\Models\Leaderboard::query()->whereRelation('committee.users', 'users.id', $user->id)->count() > 0;
    }

    public function canEdit(User $user): bool
    {
        if ($user->can('board')) {
            return true;
        }

        return $this->committee->users->contains($user);
    }
}
