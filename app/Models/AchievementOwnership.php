<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Achievement Ownership Model.
 *
 * @property int $id
 * @property int $user_id
 * @property int $achievement_id
 * @property int $alerted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Achievement $achievement
 * @property-read User $user
 *
 * @method static Builder|AchievementOwnership whereAchievementId($value)
 * @method static Builder|AchievementOwnership whereCreatedAt($value)
 * @method static Builder|AchievementOwnership whereId($value)
 * @method static Builder|AchievementOwnership whereUpdatedAt($value)
 * @method static Builder|AchievementOwnership whereUserId($value)
 * @method static Builder|AchievementOwnership whereAlerted($value)
 * @method static Builder|AchievementOwnership newModelQuery()
 * @method static Builder|AchievementOwnership newQuery()
 * @method static Builder|AchievementOwnership query()
 *
 * @mixin Eloquent
 */
class AchievementOwnership extends Model
{
    use HasFactory;

    protected $table = 'achievements_users';

    protected $guarded = ['id'];

    protected $hidden = ['user_id'];

    protected $rules = [
        'user_id' => 'required|integer',
        'achievement_id' => 'required|integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
