<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Quote Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string $quote
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Collection|QuoteLike[] $QuoteLike
 * @property-read Collection|User[] $users
 *
 * @method static Builder|Quote whereCreatedAt($value)
 * @method static Builder|Quote whereId($value)
 * @method static Builder|Quote whereQuote($value)
 * @method static Builder|Quote whereUpdatedAt($value)
 * @method static Builder|Quote whereUserId($value)
 * @method static Builder|Quote newModelQuery()
 * @method static Builder|Quote newQuery()
 * @method static Builder|Quote query()
 *
 * @mixin Eloquent
 */
class Quote extends Model
{
    use HasFactory;

    protected $table = 'quotes';

    protected $guarded = ['id'];

    protected $hidden = ['user'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /** @return BelongsToMany */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'quotes_users');
    }

    /** @return HasMany */
    public function QuoteLike()
    {
        return $this->hasMany('App\Models\QuoteLike');
    }

    /** @return array<int, QuoteLike> */
    public function likes()
    {
        $users = [];
        foreach ($this->QuoteLike as $like) {
            $users[] = $like;
        }

        return $users;
    }

    /** @return bool */
    public function liked($user_id)
    {
        foreach ($this->users as $user) {
            if ($user->id == $user_id) {
                return true;
            }
        }

        return false;
    }
}
