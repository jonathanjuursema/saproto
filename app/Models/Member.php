<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;

/**
 * Member Model.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $proto_username
 * @property string|null $membership_form_id
 * @property string|null $card_printed_on
 * @property bool $is_lifelong
 * @property bool $is_honorary
 * @property bool $is_donor
 * @property bool $is_pending
 * @property bool $is_pet
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read StorageEntry|null $membershipForm
 * @property StorageEntry|null $customOmnomcomSound
 *
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @method static QueryBuilder|Member onlyTrashed()
 * @method static QueryBuilder|Member withTrashed()
 * @method static QueryBuilder|Member withoutTrashed()
 * @method static Builder|Member whereCardPrintedOn($value)
 * @method static Builder|Member whereCreatedAt($value)
 * @method static Builder|Member whereDeletedAt($value)
 * @method static Builder|Member whereId($value)
 * @method static Builder|Member whereIsDonor($value)
 * @method static Builder|Member whereIsHonorary($value)
 * @method static Builder|Member whereIsLifelong($value)
 * @method static Builder|Member whereMembershipFormId($value)
 * @method static Builder|Member wherePending($value)
 * @method static Builder|Member whereProtoUsername($value)
 * @method static Builder|Member whereUpdatedAt($value)
 * @method static Builder|Member whereUserId($value)
 * @method static Builder|Member whereIsPending($value)
 * @method static Builder|Member whereIsPet($value)
 * @method static Builder|Member newModelQuery()
 * @method static Builder|Member newQuery()
 * @method static Builder|Member query()
 *
 * @mixin Eloquent
 */
class Member extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'members';

    protected $guarded = ['id', 'user_id'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /** @return BelongsTo */
    public function membershipForm()
    {
        return $this->belongsTo('App\Models\StorageEntry', 'membership_form_id');
    }

    /** @return BelongsTo */
    public function customOmnomcomSound()
    {
        return $this->belongsTo('App\Models\StorageEntry', 'omnomcom_sound_id');
    }

    /** @return int */
    public static function countActiveMembers()
    {
        $user_ids = [];
        foreach (Committee::all() as $committee) {
            $user_ids = array_merge($user_ids, $committee->users->pluck('id')->toArray());
        }

        return User::whereIn('id', $user_ids)->orderBy('name', 'asc')->count();
    }

    public static function countPendingMembers()
    {
        return User::whereHas('member', function ($query) {
            $query->where('is_pending', true);
        })->count();
    }

    public static function countValidMembers()
    {
        return User::whereHas('member', function ($query) {
            $query->where('is_pending', false);
        })->count();
    }

    /** @return OrderLine|null */
    public function getMembershipOrderline()
    {
        if (intval(date('n')) >= 9) {
            $year_start = intval(date('Y'));
        } else {
            $year_start = intval(date('Y')) - 1;
        }

        return OrderLine::query()
            ->whereIn('product_id', array_values(config('omnomcom.fee')))
            ->where('created_at', '>=', $year_start.'-09-01 00:00:01')
            ->where('user_id', '=', $this->user->id)
            ->first();
    }

    /** @return string|null */
    public function getMemberType()
    {
        $membershipOrderline = $this->getMembershipOrderline();

        if ($membershipOrderline) {
            switch ($this->getMembershipOrderline()->product->id) {
                case config('omnomcom.fee')['regular']:
                    return 'primary';
                case config('omnomcom.fee')['reduced']:
                    return 'secondary';
                case config('omnomcom.fee')['remitted']:
                    return 'non-paying';
                default:
                    return 'unknown';
            }
        }

        return null;
    }

    /**
     * Create an email alias friendly username from a full name.
     *
     * @param $name string
     * @return string
     */
    public static function createProtoUsername($name)
    {
        $name = explode(' ', $name);
        if (count($name) > 1) {
            $usernameBase = strtolower(Str::transliterate(
                preg_replace('/\PL/u', '', substr($name[0], 0, 1))
                .'.'.
                preg_replace('/\PL/u', '', implode('', array_slice($name, 1)))
            ));
        } else {
            $usernameBase = strtolower(Str::transliterate(
                preg_replace('/\PL/u', '', $name[0])
            ));
        }

        // make sure usernames are max 20 characters long (windows limitation)
        $usernameBase = substr($usernameBase, 0, 17);

        $username = $usernameBase;
        $i = Member::where('proto_username', $username)->withTrashed()->count();
        if ($i > 0) {
            $username = "$usernameBase-$i";
        }

        return $username;
    }
}
