<?php

namespace App\Models;

use Crypt;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /** @return BelongsToMany */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'users_mailinglists', 'list_id', 'user_id');
    }

    /**
     * @param  User  $user
     * @return bool Whether user is subscribed to mailing list.
     */
    public function isSubscribed($user)
    {
        return EmailListSubscription::where('user_id', $user->id)->where('list_id', $this->id)->count() > 0;
    }

    /**
     * @param  User  $user
     * @return bool Whether user is successfully subscribed to mailing list.
     */
    public function subscribe($user)
    {
        if (! $this->isSubscribed($user)) {
            EmailListSubscription::create([
                'user_id' => $user->id,
                'list_id' => $this->id,
            ]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param  User  $user
     * @return bool Whether user is successfully unsubscribed from mailing list.
     *
     * @throws Exception
     */
    public function unsubscribe($user)
    {
        $s = EmailListSubscription::where('user_id', $user->id)->where('list_id', $this->id);
        if ($s == null) {
            return false;
        }
        $s->delete();

        return true;
    }

    /**
     * @param  int  $user_id
     * @param  int  $list_id
     * @return string
     */
    public static function generateUnsubscribeHash($user_id, $list_id)
    {
        return base64_encode(Crypt::encrypt(json_encode(['user' => $user_id, 'list' => $list_id])));
    }

    public static function parseUnsubscribeHash(string $hash)
    {
        return json_decode(Crypt::decrypt(base64_decode($hash)));
    }
}
