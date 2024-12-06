<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Email List Subscription Model.
 *
 * @property int $id
 * @property int $list_id
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EmailList|null $emaillist
 * @property-read User|null $user
 *
 * @method static Builder|EmailListSubscription whereCreatedAt($value)
 * @method static Builder|EmailListSubscription whereId($value)
 * @method static Builder|EmailListSubscription whereListId($value)
 * @method static Builder|EmailListSubscription whereUpdatedAt($value)
 * @method static Builder|EmailListSubscription whereUserId($value)
 * @method static Builder|EmailListSubscription newModelQuery()
 * @method static Builder|EmailListSubscription newQuery()
 * @method static Builder|EmailListSubscription query()
 *
 * @mixin Eloquent
 */
class EmailListSubscription extends Model
{
    protected $table = 'users_mailinglists';

    protected $guarded = ['id'];

    /** @return HasOne */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /** @return BelongsTo */
    public function emaillist()
    {
        return $this->belongsTo(EmailList::class, 'list_id');
    }
}
