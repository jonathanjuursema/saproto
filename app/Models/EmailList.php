<?php

namespace App\Models;

use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Crypt;

/**
 * Email List Model.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $is_member_only
 * @property-read Collection|User[] $users
 *
 * @method static Builder|EmailList whereDescription($value)
 * @method static Builder|EmailList whereId($value)
 * @method static Builder|EmailList whereIsMemberOnly($value)
 * @method static Builder|EmailList whereName($value)
 * @method static Builder|EmailList newModelQuery()
 * @method static Builder|EmailList newQuery()
 * @method static Builder|EmailList query()
 *
 * @mixin Eloquent
 */
class EmailList extends Model
{
    protected $table = 'mailinglists';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_mailinglists', 'list_id', 'user_id');
    }

    /**
     * @return bool Whether user is subscribed to mailing list.
     */
    public function isSubscribed(User $user): bool
    {
        return EmailListSubscription::query()->where('user_id', $user->id)->where('list_id', $this->id)->count() > 0;
    }

    /**
     * @return bool Whether user is successfully subscribed to mailing list.
     */
    public function subscribe(User $user): bool
    {
        if (! $this->isSubscribed($user)) {
            EmailListSubscription::query()->create([
                'user_id' => $user->id,
                'list_id' => $this->id,
            ]);

            return true;
        }

        return false;
    }

    /**
     * @return bool Whether user is successfully unsubscribed from mailing list.
     *
     * @throws Exception
     */
    public function unsubscribe(User $user): bool
    {
        $s = EmailListSubscription::query()->where('user_id', $user->id)->where('list_id', $this->id);
        if ($s == null) {
            return false;
        }

        $s->delete();

        return true;
    }

    public static function generateUnsubscribeHash(int $user_id, int $list_id): string
    {
        return base64_encode(Crypt::encrypt(json_encode(['user' => $user_id, 'list' => $list_id])));
    }

    public static function parseUnsubscribeHash(string $hash): mixed
    {
        return json_decode(Crypt::decrypt(base64_decode($hash)));
    }
}
